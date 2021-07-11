/**
 * @returns {ArtPadView}
 */
function ArtPadView( ){
    
    var _view = this;
    
    /**
     * @param {String} childClass
     * @returns {Element|Boolean}
     */
    this.getContainer = function( childClass ){
        
        var container = document.getElementById( 'repository' );
        
        if( container !== null ){
            if( typeof childClass === 'string' && childClass.length ){
                var children = [].slice.call( container.children );
                for( var c = 0 ; c < children.length ; c++ ){
                    if( children[ c ].className === childClass || children[ c ].classList.contains( childClass ) ){
                        return children[ c ];
                    }
                }
                return false;
            }
            
            return container;
        }
        
        return false;
    };
    
    /**
     * Create a new HTML Element
     * @param {String} element
     * @param {Object|Array} attributes
     * @param {String|Element} content
     * @returns {Element|CodersController.element.E}
     */
    this.element = function ( element , attributes , content ){
        var e = document.createElement(element);
        //run over attributes
        if(typeof attributes === 'object') {
            for (var att in attributes) {
                if (attributes.hasOwnProperty(att)) {
                    e.setAttribute( att , attributes[ att ] );
                    //e[att] = attributes[att];
                }
            }
        }
        switch( true ){
            case ( typeof content === 'string' ):
                e.innerHTML = content;
                break;
            case (typeof content === 'object' && content instanceof Element):
                e.appendChild(content);
                break;
            case Array.isArray( content ):
                for( var c = 0 ; c < content.length ; c++ ){
                    if( typeof content[ c ] === 'object' && content[ c ] instanceof Element){
                        e.appendChild( content [ c ] );
                    }
                }
                break;
        }
        
        e.element = function( className ){
            var children = [].slice.call( this.children );
            for( var c = 0 ; c < children.length ; c++ ){
                if( children[ c ].className === className || children[ c ].classList.contains( className ) ){
                    return children[ c ];
                }
            }
            return false;
        };
        
        e.clear = function(){
            this.innerHTML = '';
        };
        
        
        return e;
    };
    /**
     * @param {Number} ms
     * @returns {CodersView}
     */
    this.wait = function( ms ){
        if( typeof ms !== 'number' ){
            ms = 1200;
        }
        console.log( 'waiting ' + (ms/1000) + ' seconds...' );
        for(var i = 0 ; i < ms ; i++ );
        return this;
    };
    /**
     * @param {Element} tabContainer
     * @returns {Element}
     */
    function initTabControl( tabContainer ){
        //create tab switcher
        var height = 0;
        //capture children
        tabContainer.Tabs = tabContainer.children.length ?
            [].slice.call(tabContainer.children[0].children) :
            [];
        //capture first tab
        tabContainer.firstTab = function(){
            return this.Tabs[0].getAttribute('data-tab');
        };
        //apply height fix
        tabContainer.Tabs.forEach( function(tab){
            var th = 0;
            for( var i = 0 ; i < tab.children.length ; i++ ){
                th += tab.children[ i ].offsetHeight;
                //console.log( tab.children[ i ].style );
            }
            //console.log( th ) ;
            if( th > height ){
                height = th;
            }
        });
        //attach tab-toggle event
        tabContainer.toggleTab = function( tab ){
            if( typeof tab === 'undefined' ){
                tab = this.firstTab();
            }
            this.Tabs.forEach( function( panel ){
                var name = panel.getAttribute('data-tab');
                //console.log(name);
                if( name === tab ){
                    panel.classList.add('active');
                }
                else{
                    panel.classList.remove('active');
                }
            });
            [].slice.call(tabContainer.TabMenu().children).forEach( function(item){
                var name = item.getAttribute('data-tab');
                if( name === tab ){
                    item.classList.add('active');
                }
                else{
                    item.classList.remove('active');
                }
            });
            return this;
        };
        //create tab menu
        var tabs = [];
        tabContainer.Tabs.forEach(function(tab){
            var title = tab.getAttribute('data-tab');
            var item = _view.element('li',{'class':'item','data-tab':title},title);
            item.addEventListener('click',function(e){
                e.preventDefault();
                var name = this.getAttribute('data-tab');
                tabContainer.toggleTab(name);
                return false;
            });
            tabs.push(item);
        });
        tabContainer.prepend( _view.element('ul',{'class':'tab-menu inline'},tabs));
        tabContainer.TabMenu = function(){ return this.children[0]; };
        tabContainer.toggleTab();
        console.log('ASAS');
        console.log( tabContainer.style.height);
        //apply tab-height fix
        tabContainer.style.height = height + 'px';
        return tabContainer;
    };
    /**
     * @returns {ArtPadView}
     */
    this.init = function(){

        var element = document.getElementsByClassName('tab-container');
        var tabs =  ( element !== null ) ? [].slice.call( element ) : [];
        tabs.forEach( function( tabControl ){
            initTabControl( tabControl );
        });
        
        return this;
    };
    
    return this.init();
}

(function(){
    
    document.addEventListener('DOMContentLoaded',function(e){
        
        new ArtPadView();
        
    });
    
})(/*autoexecute*/);



