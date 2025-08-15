<?php

namespace SM_Elementor\Module\Query\Module;


class module_1 extends Common\Base {


    function get_template()
    {
        return <<<EOT

        <article {{container.attrs}}>
                      
           <div {{content.attrs}}>
                
                <div {{media.attrs}}>
    
                    {{thumb}}
                    
                    {{lightbox_link}}                
                   
                    {{media.children}}
    
                    <div {{info.attrs}}>
    
                        {{info.children elements="terms,title,excerpt,author,date,readmore"}}
                        
                    </div>
                                     
                </div>             
              
           </div>
          
        </article>

EOT;
    }



}