###############################################################################
## Copyright 2005-2016 OCSInventory-NG/OCSInventory-Server contributors.
## See the Contributors file for more details about them.
## 
## This file is part of OCSInventory-NG/OCSInventory-ocsreports.
##
## OCSInventory-NG/OCSInventory-Server is free software: you can redistribute
## it and/or modify it under the terms of the GNU General Public License as
## published by the Free Software Foundation, either version 2 of the License,
## or (at your option) any later version.
##
## OCSInventory-NG/OCSInventory-Server is distributed in the hope that it
## will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
## of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.
##
## You should have received a copy of the GNU General Public License
## along with OCSInventory-NG/OCSInventory-ocsreports. if not, write to the
## Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
## MA 02110-1301, USA.
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

    my $verif_query = "SELECT * FROM `greenit` WHERE HARDWARE_ID = ?";
    my @verif_arg = ($hardware_id);
    my $verif_result = _prepare_sql($verif_query, @verif_arg);
    my $verif_value_result = undef;
    if(!defined $verif_result) { return INVENTORY_CONTINUE; }

    while(my $row = $verif_result->fetchrow_hashref()) {
        $verif_value_result->{$row->{DATE}}->{UPTIME} = $row->{UPTIME};
    }

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


    foreach my $session (@{$xml->{CONTENT}->{GREENIT}}) {
        my $verif_query = "SELECT * FROM `greenit` WHERE HARDWARE_ID = ? AND DATE = ?";
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