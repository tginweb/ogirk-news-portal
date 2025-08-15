<?php

namespace SM_Elementor\Module\Query\Module;


class module_2 extends Common\Base {


    function get_title_class() {
        return ['s-typo-btitle-3'];
    }

    function get_template()
    {
        return <<<EOT

        <article {{container.attrs}}>
                
          <div {{content.attrs}}>
                          
            <div {{media.attrs}}>

                {{thumb}}
                           
                {{media.children}}
                                                                    
            </div>             
                      
            <div {{info.attrs}}>               
                
                {{info.children elements="terms,title,excerpt,author,date,readmore"}}
                
            </div>
          
          </div>
            
        </article>

EOT;
    }




}