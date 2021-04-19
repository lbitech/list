<?php
    if ( $values['status'] == 'EM&nbsp;ATRASO'        ) { $css = 'red pulse'; }
elseif ( $values['status'] == 'Em&nbsp;Andamento') { $css = 'light-green accent-4';        }
elseif ( $values['status'] == 'A&nbsp;Fazer'           ) { $css = 'cyan accent-3';         }
elseif ( $values['status'] == 'Em&nbsp;Projeto') { $css = 'light-green accent-4';        }
elseif ( $values['status'] == 'Encaminhado&nbsp;Para&nbsp;Compras'           ) { $css = 'yellow lighten-4';       }
elseif ( $values['status'] == 'Comprado'           ) { $css = 'amber';       }
elseif ( $values['status'] == 'Recebido'           ) { $css = 'amber darken-4';       }
elseif ( $values['status'] == 'Em&nbsp;Montagem') { $css = 'light-green darken-4';        }
elseif ( $values['status'] == 'Concluido'           ) { $css = 'black';        }
