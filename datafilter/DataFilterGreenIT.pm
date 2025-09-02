###############################################################################
## OCSINVENTORY-NG
## Copyleft Antoine ROBIN 2023
## Web : http://www.ocsinventory-ng.org
##
## This code is open source and may be copied and modified as long as the source
## code is always made freely available.
## Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
################################################################################

package Apache::Ocsinventory::Plugins::Greenit::DataFilterGreenIT;

use strict;

BEGIN {
    if($ENV{'OCS_MODPERL_VERSION'} == 1) {
        require Apache::Ocsinventory::Server::Modperl1;
        Apache::Ocsinventory::Server::Modperl1->import();
    } elsif($ENV{'OCS_MODPERL_VERSION'} == 2) {
        require Apache::Ocsinventory::Server::Modperl2;
        Apache::Ocsinventory::Server::Modperl2->import();
    }
}

use Apache::Ocsinventory::Server::System;
use Apache::Ocsinventory::Server::Communication;
use Apache::Ocsinventory::Server::Constants;
use Apache::Ocsinventory::Interface::Database;
use Apache::Ocsinventory::Map;
use DateTime;

# Initialize option
push @{$Apache::Ocsinventory::OPTIONS_STRUCTURE}, {
    'NAME' => 'DATAFILTER',
    'HANDLER_PROLOG_READ' => undef, #or undef # Called before reading the prolog
    'HANDLER_PROLOG_RESP' => undef, #or undef # Called after the prolog response building
    'HANDLER_PRE_INVENTORY' => \&greenit_pre_inventory, #or undef # Called before reading inventory
    'HANDLER_POST_INVENTORY' => \&greenit_post_inventory, #or undef # Called when inventory is stored without error
    'REQUEST_NAME' => undef,  #or undef # Value of <QUERY/> xml tag
    'HANDLER_REQUEST' => undef, #or undef # function that handle the request with the <QUERY>'REQUEST NAME'</QUERY>
    'HANDLER_DUPLICATE' => undef,#or undef # Called when a computer is handle as a duplicate
    'TYPE' => OPTION_TYPE_SYNC, # or OPTION_TYPE_ASYNC ASYNC=>with pr without inventory, SYNC=>only when inventory is required
    'XML_PARSER_OPT' => {
        'ForceArray' => ['']
    }
};

sub _prepare_sql {
    my ($sql, @arguments) = @_;
    my $query;
    my $i = 1;
    
    my $dbh = $Apache::Ocsinventory::CURRENT_CONTEXT{'DBI_HANDLE'};
    $query = $dbh->prepare($sql);

    foreach my $value (@arguments) {
        if(defined $value) {
            $query->bind_param($i, $value);
            $i++;
        }
    }
    
    $query->execute or return undef; 

    return $query;   
}

sub greenit_pre_inventory {
    &_log(1,'datafiltergreenit',"pre_process") if $ENV{'OCS_OPT_LOGLEVEL'};

    my $current_context = shift;
    our $xml = $current_context->{'XML_ENTRY'};
    my $hardware_id = $Apache::Ocsinventory::CURRENT_CONTEXT{'DATABASE_ID'};
    my $datetime = DateTime->now->strftime('%Y-%m-%d');
    my $greenit_hash = {};
    my $index = 0;
    my $indexbis = 0;

    # Check if data from the inventoried machine already exist
    my $verif_query = "SELECT * FROM `greenit` WHERE HARDWARE_ID = ?";
    my @verif_arg = ($hardware_id);
    my $verif_result = _prepare_sql($verif_query, @verif_arg);
    my $verif_value_result = undef;
    if(!defined $verif_result) { return INVENTORY_CONTINUE; }

    # Retrieve existing data for comparing with new data
    while(my $row = $verif_result->fetchrow_hashref()) {
        $verif_value_result->{$row->{DATE}}->{UPTIME} = $row->{UPTIME};
    }

    # Delete all data which not have the same date than datetime
    if (ref($xml->{CONTENT}->{GREENIT}) eq 'ARRAY') {
        for my $session (@{$xml->{CONTENT}->{GREENIT}}) {        
            if(($session->{DATE} ne $datetime) && ($session->{UPTIME} eq $verif_value_result->{$session->{DATE}}->{UPTIME})) {
                delete $xml->{CONTENT}->{GREENIT}[$index];
            }
            if(defined($xml->{CONTENT}->{GREENIT}[$index])) {
                $greenit_hash->{GREENIT}[$indexbis] = $xml->{CONTENT}->{GREENIT}[$index];
                $indexbis++;
            }
            $index++;
        }

        $xml->{CONTENT}->{GREENIT} = $greenit_hash->{GREENIT};
    }

    return INVENTORY_CONTINUE;
}

sub greenit_post_inventory {
    &_log(1,'datafiltergreenit',"post_process") if $ENV{'OCS_OPT_LOGLEVEL'};

    my $current_context = shift;
    our $xml = $current_context->{'XML_ENTRY'};
    my $hardware_id = $Apache::Ocsinventory::CURRENT_CONTEXT{'DATABASE_ID'};
    my $datetime = DateTime->now->strftime('%Y-%m-%d');
    my $greenit_post_process = 1;
    my @id_to_delete;
    my @column_paramaters;
    my @arguments_insert;

    # Retrieve all existing data from the database and update all by deleting and insering new one
    my $verif_query = "SELECT * FROM `greenit` WHERE HARDWARE_ID = ? AND DATE = ?";

    if (ref($xml->{CONTENT}->{GREENIT}) eq 'ARRAY') {
        foreach my $session (@{$xml->{CONTENT}->{GREENIT}}) {
            my @verif_arg = ($hardware_id, $session->{DATE});
            my $verif_result = _prepare_sql($verif_query, @verif_arg);
            if(!defined $verif_result) { return INVENTORY_CONTINUE; }

            while(my $row = $verif_result->fetchrow_hashref()) {
                push @id_to_delete, $row->{ID};
            }

            push @column_paramaters, "(?, ?, ?, ?)";
            push @arguments_insert, $hardware_id;
            push @arguments_insert, $session->{DATE};
            push @arguments_insert, $session->{CONSUMPTION};
            push @arguments_insert, $session->{UPTIME};
        }
    }
    else {
        my @verif_arg = ($hardware_id, $xml->{CONTENT}->{GREENIT}->{DATE});
        my $verif_result = _prepare_sql($verif_query, @verif_arg);
        if(!defined $verif_result) { return INVENTORY_CONTINUE; }

        while(my $row = $verif_result->fetchrow_hashref()) {
            push @id_to_delete, $row->{ID};
        }

        push @column_paramaters, "(?, ?, ?, ?)";
        push @arguments_insert, $hardware_id;
        push @arguments_insert, $xml->{CONTENT}->{GREENIT}->{DATE};
        push @arguments_insert, $xml->{CONTENT}->{GREENIT}->{CONSUMPTION};
        push @arguments_insert, $xml->{CONTENT}->{GREENIT}->{UPTIME};
    }

    if(@id_to_delete) {
        my $delete_query = "DELETE FROM `greenit` WHERE ID IN (";
        $delete_query .= join ",", @id_to_delete;
        $delete_query .= ")";
        my $delete_result = _prepare_sql($delete_query, undef);
        my $increment_query = "ALTER TABLE `greenit` AUTO_INCREMENT 0";
        my $increment_result = _prepare_sql($increment_query, undef);
        if(!defined $delete_result) { $greenit_post_process = 0; }
    }

    if(@arguments_insert) {
        my $insert_query = "INSERT INTO `greenit` (HARDWARE_ID, DATE, CONSUMPTION, UPTIME) VALUES ";
        $insert_query .= join ",", @column_paramaters;
        my $insert_result = _prepare_sql($insert_query, @arguments_insert);
        if(!defined $insert_result) { $greenit_post_process = 0; }
    }

    if($greenit_post_process == 0) {
        &_log(1,'datafiltergreenit',"error:greenit_post_process") if $ENV{'OCS_OPT_LOGLEVEL'};
        return INVENTORY_CONTINUE;
    }
}