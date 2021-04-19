<?php
    if ( $values['status'] == 'WICHTIG'       ) { $css = 'orange pulse'; }
elseif ( $values['status'] == 'IN&nbsp;ARBEIT') { $css = 'green';        }
elseif ( $values['status'] == 'ERLEDIGEN'     ) { $css = 'blue';         }
elseif ( $values['status'] == 'IDEE'          ) { $css = 'indigo';       }
elseif ( $values['status'] == 'FERTIG'        ) { $css = 'black';        }
