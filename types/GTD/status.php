<?php
    if ( $values['status'] == 'In'                 ) { $css = 'orange pulse'; }
elseif ( $values['status'] == 'Next&nbsp;actions'  ) { $css = 'green';        }
elseif ( $values['status'] == 'Waiting&nbsp;for'   ) { $css = 'blue';         }
elseif ( $values['status'] == 'Project'            ) { $css = 'teal';         }
elseif ( $values['status'] == 'Some&nbsp;day/maybe') { $css = 'indigo';       }
elseif ( $values['status'] == 'DONE'               ) { $css = 'black';        }
