<?php
    if ( $values['status'] == 'Must&nbsp;have'  ) { $css = 'orange pulse'; }
elseif ( $values['status'] == 'Should&nbsp;have') { $css = 'green';        }
elseif ( $values['status'] == 'Could&nbsp;have' ) { $css = 'blue';         }
elseif ( $values['status'] == "Won't&nbsp;have" ) { $css = 'indigo';       }
elseif ( $values['status'] == 'Ready'           ) { $css = 'black';        }
