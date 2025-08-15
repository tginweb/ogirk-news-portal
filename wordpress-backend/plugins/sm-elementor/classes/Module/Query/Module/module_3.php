<?php

namespace SM_Elementor\Module\Query\Module;


class module_3 extends Common\Base {

    function get_template()
    {
        return <<<EOT

        <article {{container.attrs}}>
        
            <div {{content.attrs}}>
                    
                <div class="row m-row">
                
                    <div class="col-md-4 m-row-col">
                                    
                        <div {{media.attrs}}>
                        
                            {{thumb}}
                            
                            {{media.children}}
                            
                        </div>
                        
                    </div>
                            
                    <div class="col-md-8 m-row-col">
        
                        <div {{info.attrs}}>
                            
                            {{info.children elements="terms,title,excerpt,date,readmore"}}
                            
                        </div>
                            
                    </div>
                
                </div>
              
            </div>
            
            <div class="m-delim-h m-delim-h-bottom"></div>
            
        </article>

EOT;
    }

}