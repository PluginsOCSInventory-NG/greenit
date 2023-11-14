###############################################################################
## OCSINVENTORY-NG
## Copyleft Antoine ROBIN 2023
## Web : http://www.ocsinventory-ng.org
##
## This code is open source and may be copied and modified as long as the source
## code is always made freely available.
## Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
################################################################################

package Apache::Ocsinventory::Plugins::Greenit::Map;
 
use strict;
use Apache::Ocsinventory::Map;

$DATA_MAP{greenit} = {
   mask => 0,
   multi => 1,
   auto => 1,
   delOnReplace => 0,
   sortBy => 'DATE',
   writeDiff => 0,
   cache => 0,
   fields => {
       DATE => {},
       CONSUMPTION => {},
       UPTIME => {},
   }
};
1;