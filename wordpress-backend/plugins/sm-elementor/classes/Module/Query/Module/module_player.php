<?php

namespace SM_Elementor\Module\Query\Module;


class module_player extends Common\Base {

    function get_template()
    {
        return <<<EOT

        <article {{container.attrs}}>
                      
            {{player}}
           
            {{info.children elements="title,date"}}
           
        </article>

EOT;
    }




}