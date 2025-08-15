<?php

namespace SM_Elementor\Module\Query\Module;


class module_4 extends Common\Base {


    function get_template()
    {
        return <<<EOT

        <article {{container.attrs}}>
        
            <div {{content.attrs}}>
                                           
                <div {{info.attrs}}>
                    
                    {{info.children elements="title,date"}}
                 
                </div>
                                              
            </div>
            
            <div class="m-delim-h m-delim-h-bottom"></div>
            
        </article>

EOT;
    }



    function get_container_classes()
    {
        $classes = parent::get_container_classes();

        if (has_term('is-bold', 'sm-role', $this->entity->host))
            $classes[] = 's-bold';

        return $classes;
    }

}