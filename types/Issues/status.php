<?php
    if ( $values['status'] == 'URGENT'         ) { $css = 'orange pulse'; }
elseif ( $values['status'] == 'IN&nbsp;PROCESS') { $css = 'green';        }
elseif ( $values['status'] == 'TODO'           ) { $css = 'blue';         }
elseif ( $values['status'] == 'IDEA'           ) { $css = 'indigo';       }
elseif ( $values['status'] == 'DONE'           ) { $css = 'black';        }
