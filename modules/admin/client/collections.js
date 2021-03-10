/**
 * @returns {CollectionView}
 */
function CollectionView( ){
    /**
     * @type CollectionView
     */
    var _view = this;
    /**
     * @type CollectionModel
     */
    var _server = null;
    
    var _controls = {
        'navigator' : null,
        'form':null,
        //'title': null,
        'grid_size':[2,3,4,6,8]
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
     * @returns {CollectionView}
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
        }
        
        return container;
    };
    /**
     * @returns {Number}
     */
    this.getParent = function(){
        
        var collection = this.getContainer('collection');
        
        return collection.hasAttribute('data-id') ?
                parseInt( collection.getAttribute('data-id') ) :
                        0;
    };
    /**
     * 
     * @param {Array} resources
     * @param {Number} parent_id 
     * @param {Object|Boolean} navigator
     * @returns {CollectionView}
     */
    this.collection = function( resources , id ){
        var container = _view.getContainer('collection');
        if( container !== false ){
            container.clear();
            container.setAttribute('data-id',id);
            if( id > 0 ){
                _view.getContainer('post-data').classList.remove('hidden');
            }
            else{
                _view.getContainer('post-data').classList.add('hidden');
            }
            for( var r = 0 ; r < resources.length ; r++ ){
                container.appendChild( _view.addItem( resources[ r ] ) );
            }
        }
        return this;
    };
    /**
     * @param {Array} path
     * @param {Number} current
     * @returns {CollectionView}
     */
    this.path = function( path , current ){

        if( typeof current === 'undefined' ){
            current = 0;
        }

        var navPath = _controls.navigator;
        
        navPath.clear();

        var ids = Object.keys( path );
        
        if( ids.length ){
            var home = _view.element('li',{'class':'home link'},'Home');
            home.addEventListener('click',function(e){
                e.preventDefault();
                e.stopPropagation();
                _server.collection(_view.collection).path(_view.path);
                return false;
            });
            navPath.appendChild( home );
            ids.forEach( function( id ){
                if( parseInt( id ) !== current ){
                    var parent = _view.element('li',{'class':'link','data-id':id}, path[ id ] );
                    parent.addEventListener('click',function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        _server.collection(_view.collection,id).path(_view.path , id );
                        return false;
                    });
                    navPath.appendChild( parent );
                }
                else{
                    navPath.appendChild( _view.element('li',{
                        'class':'current'
                    }, path[ id ] ) );
                }
            });
        }
        else{
            navPath.appendChild( _view.element('li',{
                'class':'home'
            },'Home'));
            //txtTitle.innerHTML = 'Collection';
        }
        
        
        return this;
    };
    /**
     * @param {Number} ID
     * @returns {Element|Boolean}
     */
    this.getItem = function( ID ){
        var collection = this.getContainer('collection').children;
        //console.log( collection);
        for( var i = 0 ; i < collection.length ; i++ ){
            var resId = collection[ i ].getAttribute('data-id');
            //console.log( resId );
            if( resId !== null && resId == ID ){
                return collection[ i ];
            }
        }
        return false;
    };
    /**
     * @param {String} message 
     * @returns {CodersController}
     */
    this.notify = function( message ){
        
        document.querySelectorAll('');
        
        return this;
    };
    /**
     * @param {Boolean} status
     * @returns {CollectionView}
     */
    this.sync = function( status ){
        
        if( typeof status !== 'boolean' ){
            status = false;
        }
        
        if( status ){
            _view.getContainer().classList.add(['sync','disabled']);
        }
        else{
            _view.getContainer().classList.remove(['sync','disabled']);
        }
        
        return this;
    };
    /**
     * Collapse Source resource into the parent resource by ID
     * @param {Number} source_id
     * @param {Number} target_id
     * @returns {CollectionView}
     */
    this.attach = function( source_id , target_id ){
        //console.log('Attaching ' + source_id + ' into ' + target_id  + ' ...');
        var resource = _view.getItem( source_id );
        //console.log(resource);
        if( false !== resource ){
            resource.remove();
            //console.log( source_id + ' removed!');
            var target = _view.getItem( target_id );
            if( false !== target ){
                target.classList.add('attached');
                window.setTimeout(function(){
                    target.classList.remove('attached');
                }, 2000 );
            }
        }
        
        return this;
    };
    /**
     * @param {String|Number} source_id
     * @returns {CollectionView}
     */
    this.remove = function( id ){
        //console.log('Attaching ' + source_id + ' into ' + target_id  + ' ...');
        var resource = _view.getItem( id );
        if( false !== resource ){
            resource.remove();
        }
        return this;
    };
    /**
     * @param {Object} attributes
     * @returns {Image}
     */
    this.image = function( attributes ){
        var img = new Image();
        //run over attributes
        if(typeof attributes === 'object') {
            for (var att in attributes) {
                if (attributes.hasOwnProperty(att)) {
                    img[att] = attributes[att];
                }
            }
        }
        //append image format
        img.onload = function(e){
            //console.log('Loading image ...');
            if( this.width > this.height ){
                this.classList.add('landscape');
            }
            if( this.width < this.height ){
                this.classList.add('portrait');
            }
        };
        return img;
    };
    /**
     * @param {Object} itemData
     * @returns {CollectionView}
     */
    this.showPost = function( itemData ){
        
        //console.log( itemData );
        
        if( itemData.hasOwnProperty('error')){
            
            return this;
        }
        
        var media = _controls.form.element('media');
        var txtTitle = _controls.form.element('title');
        var txtContent = _controls.form.element('content');

        txtTitle.value = itemData.title;
        txtTitle.placeholder = itemData.name;
        txtContent.value = itemData.content;
        media.innerHTML = '';
        if( _server.isImage( itemData.type ) ){
            media.classList.remove('hidden');
            media.appendChild( _view.image( {
                'alt':itemData.name,
                'title':itemData.title,
                'src':_server.resourceURL(itemData.public_id)
            } )  );
        }
        else{
            media.classList.add('hidden');
        }
            
        //var collection = this.getContainer('collection');
        
        _server.collection( _view.collection , itemData.ID ).path( _view.path , itemData.ID );

        return this;
    };
    /**
     * li.item
     *      div.content
     *              a.type[file-type]
     *                  img alt title
     *                  span[name]
     * @param {Object} itemData
     * @returns {Element}
     */
    this.addItem = function( itemData ){

        var contentClass = ['content'];

        if( _server.isImage( itemData.type ) ){
            contentClass.push('image');
        }
        else if( _server.isDocument( itemData.type )){
            contentClass.push('text');
        }
        
        var btnTitle = this.element('span',
                {'class': 'caption title' } ,
                itemData.title.length ? itemData.title : itemData.name );
        var btnOpen = this.element('a',{
                'class':'action open dashicons dashicons-admin-links top-right rounded',
                'target':'_blank',
                'title':'Display',
                'href': _server.resourceURL(itemData.public_id)});
        var btnRemove = this.element('span',{
                'class':'action remove dashicons dashicons-trash bottom-left rounded',
        },'');

        btnTitle.addEventListener( 'click' , function(e){
            e.preventDefault();
            e.stopPropagation();
            //CollectionView.Drag.release();
            //_view.getContainer('collection').enable();
            var id = this.parentNode.parentNode.getAttribute('data-id');
            if( null !== id ){
                id = parseInt( id );
                _server.item( _view.showPost , id );
            }
            return false;
        });
        
        btnRemove.addEventListener('click',function(e){
                e.preventDefault();
                e.stopPropagation();
                //CollectionView.Drag.release();
                //_view.getContainer('collection').enable();
                _server.remove( itemData.ID, _view.remove );
                return false;
            });

        var elements = [btnTitle,btnOpen,btnRemove];

        if( _view.getParent( ) > 0 ){
            var btnParent = this.element('span',{
                    'class':'action move-up dashicons dashicons-arrow-up-alt top-left rounded'
            },'');

            btnParent.addEventListener( 'click', function(e){
                e.preventDefault();
                e.stopPropagation();
                //CollectionView.Drag.release();
                //_view.getContainer('collection').enable();
                var item = this.parentNode.parentNode;
                var id = item.getAttribute('data-id');
                //_server.attach( id , 0 , _view.attach );
                _server.up( id , function(){
                    item.remove();
                    //console.log( item );
                    //console.log( id + ' moved up!');
                });
                return false;
            });
            elements.push( btnParent );
        }

        if( _server.acceptedTypes( true ).includes( itemData.type ) ){
            var img = this.image({
                //'class': '',
                'alt':itemData.name,
                'title':itemData.name,
                'src':_server.resourceURL(itemData.public_id)
            });
            elements.unshift( img );
        }
        
        //console.log(_server.urlRoot());
        var item = this.element('li',{
            'class':'item',
            'draggable':'true',
            'data-id':itemData.ID},
            //'data-id':itemData.public_id},
            this.element('div',{'class':contentClass.join(' ')},elements) );

        apply_drag_drop_v2( item );

        return item;
    };
    /**
     * @param {Function} caller
     * @returns {Element}
     */
    this.progressBar = function( caption , cls ){
        
        var progressBar = this.element('div',{'class': 'progress-bar ' + ( cls || '' ) },[
            this.element('span',{'class':'step'}),
            this.element('span',{'class':'caption'})
        ]);
        /**
         * Define a fast child catch
         * @param {String} cls
         * @returns {Element|Boolean}
         */
        progressBar.getElement = function( cls ){
            for( var e = 0 ; e < this.childNodes.length ; e++ ){
                if( this.childNodes[ e ].classList.contains( cls ) ){
                    return this.childNodes[ e ];
                }
            }
            return false;
        };
        //define a label overrider
        progressBar.setLabel = function( text ){
            var label = this.getElement( 'caption' );
            if( false !== label ){
                label.innerText = text;
            }
            return this;
        };
        //define a progress event call
        progressBar.update = function( progress ){
            this.dispatchEvent( new CustomEvent( 'ProgressBarStep' , {
                'detail':{'progress':progress,'ts': new Date() },
                'bubbles': false,
                'cancelable': true
            } ) );
            return this;
        };
        //define a progress event handler
        progressBar.addEventListener( 'ProgressBarStep' , function( e ){
            var bar = progressBar.getElement('step');
            //var caption = progressBar.getElement('caption');
            if( false !== bar ){
                var status = !Number.isNaN( e.detail.progress ) ?
                        Number.parseInt( e.detail.progress ) : 0;
                if( status > 100 ){
                    status = 100;
                }
                else if( status < 0 ){
                    status = 0;
                }
                bar.style = 'width:' + status + '%';
                //caption.innerHTML = status + '%';
            }
            
        } , false );
        
        return progressBar.setLabel( caption || '' );
    };
    /**
     * @param {String} name
     * @returns {Element}
     */
    this.prepareForm = function( name ){
        var formData = this.element('form',{
            'name': name || 'upload',
            'method':'POST',
            'action':this.url({'action':'upload'}),
            'enctype':'multipart/form-data',
            'class':'form-container step'
        });
        return formData;
    };
    /**
     * @param {HTMLElement} element
     * @returns {HTMLElement}
     */
    function apply_drag_drop_v2( element ){
        if( element instanceof HTMLElement ){
            var event_list = ['click','mouseup','mousedown','mouseout','mouseenter'];
            event_list.forEach( function( event ){
                element.addEventListener( event , function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    switch( event ){
                        case 'mouseup':
                            var target_id = CollectionView.Drag.dragOver( this );
                            if( target_id > 0 ){
                                console.log( CollectionView.Drag.ID + ':' + target_id );
                                _server.attach(
                                        CollectionView.Drag.ID ,
                                        target_id ,
                                        function(){
                                            console.log( CollectionView.Drag.ID + ' stacked into ' + target_id );
                                        } );
                                CollectionView.Drag.release( true );
                                return true;
                            }
                            break;
                        case 'mousedown':
                            if( CollectionView.Drag.capture( this ) ){
                                
                            }
                            break;
                        case 'mouseenter':
                            if( CollectionView.Drag.dragOver( this ) ){
                                
                            }
                            break;
                        case 'mouseout':
                            CollectionView.Drag.dragOut( this );
                            break;
                        case 'click':
                            CollectionView.Drag.release();
                            break;
                    }
                    return true;
                });
            });
        }
        return element;
    };
    /**
     * @returns {HTMLElement}
     */
    function render_uploader(  ){

        var inputFileSize = _view.element('input',{'type':'hidden',
                'name':'MAX_FILE_SIZE',
                'value':CollectionView.FileSize()});
            
        var inputFiles = _view.element('input',{
                'class':'hidden',
                'id': 'id_uploader',
                'type':'file',
                'name':'upload[]',
                'multiple':true,
                //'accept':this.acceptedTypes().join(', '),
                //'id': _repo.inputs.dropzone + '_input'
            });
        var inputButton = _view.element('button',{
                'class':'button button-primary dashicons-before dashicons-upload',
                'type':'submit',
                'name':'_action',
                'value':'default'
            }, 'Upload' );
            
        inputButton.addEventListener( 'click', function(e){
            //e.preventDefault();
            this.value = _view.selectedTab();
            //console.log( this.name + ':' + this.value );
            return true;
        });
            
        //capture upload events
        inputFiles.addEventListener( 'change', function(e){
                var fileList = this.files;
                //console.log( fileList );
                return true;
            });

        var url = 'http://localhost/WORDPRESS/artistpad/wp-admin/admin.php?page=artpad-collection&_action=upload';
         
        var formData = _view.element('form',{
            //FORM DECLARATION
            'name': 'collection',
            'method':'POST',
            'action': url,
            'enctype':'multipart/form-data'
        },[
            //FORM ELEMENTS
            inputFileSize,
            inputFiles,
            inputButton
        ]);
        
        var uploader = _view.element('div',{'class':'uploader item container collapsed' },[
            formData,
            _view.element('label',{
                'class':'dashicons-before dashicons-media-default button',
                'for': 'id_uploader',
            }, 'Select files' ),
            _view.progressBar( 'Upload' , 'hidden content' )
        ]);
        
        uploader.addEventListener( 'click', e => {
                //e.preventDefault();
                e.stopPropagation();
                return false;
            });


        return uploader;
    };
    /**
     * @param {String} cls
     * @returns {HTMLElement}
     */
    function render_grid_resizer( cls ){
        
        var sizes = _controls.grid_size;
        
        var toolBar = _view.element('ul',{'class':'grid inline ' + cls}, '' );
        
        toolBar.select = function( size ){
            var collection = _view.getContainer('collection');
            [].slice.call( this.children ).forEach( function( item ){
                var n = item.getAttribute('data-size');
                var cls = 'grid-' + n;
                if( n === size ){
                    if( !item.classList.contains('button-primary') ){
                        item.classList.add('button-primary');
                        collection.classList.add( cls );
                    }
                }
                else if( item.classList.contains('button-primary')){
                    item.classList.remove('button-primary');
                    collection.classList.remove( cls );
                }
            });
        };
        
        sizes.forEach( function( size ){
            var item = _view.element('li',{'class':'option button','data-size':size},'x' +  size.toString( ) );
            item.addEventListener('click',function(e){
                e.preventDefault();
                var clicked = this.getAttribute('data-size');
                _server.grid( clicked , function(size){
                    console.log( 'Grid size ' + size );
                });
                toolBar.select( clicked );
                return false;
            });
            toolBar.appendChild( item );
        });

        return toolBar;
    };
    /**
     * @returns {HTMLElement}
     */
    function render_toolbox(){
        
        _controls.navigator = _view.element('ul',{'class':'navigator panel left inline'});
        
        var btnUploader = _view.element('span',{'class':'button right'},'Upload');
        btnUploader.addEventListener( 'click' , function(e){
            e.preventDefault();
            e.stopPropagation();
            var uploader = _view.getContainer('uploader');
            if( false !== uploader ){
                uploader.classList.toggle('collapsed');
            }
            return true;
        });

        return _view.element('div',{'class': 'toolbox container solid clearfix centered'},[
                _controls.navigator,
                btnUploader,
                render_grid_resizer('panel right')
            ]);
    }
    /**
     * @returns {HTMLElement}
     */
    function render_post(){
        
        //create here the post form
        var txtTitle = _view.element('input',{'type':'text','name':'title','id':'id_title','class':'title'});
        var txtContent = _view.element('textarea',{'name':'content','id':'id_content','class':'content'});
        var btnUpdate = _view.element('button',{'type':'submit','name':'_action','value':'save','class':'button button-primary big'},'Save');
        var media = _view.element('div',{'class':'media'});
        //var imgMedia = _view.image({'src'});
        
        _controls.form = _view.element('div',{'class':'container post-data hidden solid'},[
            txtTitle,
            media,
            txtContent,
            btnUpdate
        ]);
        
        
        return _controls.form;
    };
    /**
     * @returns {HTMLElement}
     */
    function render_collection(){
        
        var collection = _view.element('ul', {'class': 'collection grid-4' })
        
        collection.enable = function(){
            if( this.classList.contains('disabled') ){
                this.classList.remove('disabled');
            }
            return this;
        };
        collection.disable = function(){
            this.classList.add('disabled');
            return this;
        };
        
        var container = document.getElementById('wpbody-content');
        ['click','mouseup','keypress'].forEach( function( event ){
            container.addEventListener( event ,function(e){
                e.preventDefault();
                e.stopPropagation();
                if( CollectionView.Drag.ID ){
                    switch( true ){
                        case event === 'mouseup':
                        case event === 'click' && 'which' in e && e.which === 3:
                        case event === 'click' && 'button' in e && e.button === 2:
                        case event === 'keypress' && 'which' in e && e.which === 27:
                        case event === 'keypress' && 'keycode' in e && e.keycode === 27:
                            CollectionView.Drag.release();
                            return false;
                    }
                }
                return true;
            });
        });
        
        return collection;
    };
    /**
     * @param {array} collections 
     * @returns {CollectionView}
     */
    this.initialize = function( ){

        console.log('Init client ...');
        
        _server = new CollectionModel( _view.sync );
        
        var container = _view.getContainer();
        
        if( null !== container ){
            
            container.appendChild( render_toolbox( ) );
            container.appendChild( render_post( ) );
            container.appendChild( render_uploader( ) );
            container.appendChild( render_collection() );
            //request parent collection (root)
            _server.collection( _view.collection ).path(_view.path);
            //_server.grid( 0 , _view.getContainer('toolbox').element('grid').select );
        }
        else{
            console.log('Container not found');
        }

        return this;
    };
    
    return this.initialize( );
}
/**
 * @returns {Number}
 */
CollectionView.FileSize = function(){ return 256 * 256 * 256; };
/**
 * @type CollectionView.Drag
 */
CollectionView.Drag = {
    'ID':0,
    'timer':0,
    'elapsed': 600,
    'Icon':{
        'getDraggable':function(){
            var drag = document.getElementById('artpad-draggable');
            if( drag === null ){
                drag = document.createElement('div');
                drag.id = 'artpad-draggable';
                drag.move = function( x , y ){
                    this.style.left = ( x - this.style.width + 24 ) + 'px';
                    this.style.top = ( y - this.style.height + 24 ) + 'px';
                };
                document.body.appendChild(drag);
            }
            return drag;
        },
        'show':function( ){
            var drag = this.getDraggable();
            window.addEventListener('mousemove',function(e){
                //console.log(e.offsetX);
                drag.move( e.clientX , e.clientY );
            });
            drag.classList.add('show');
            return this;
        },
        'clear':function( ){
            var drag = document.getElementById('artpad-draggable');
            if( drag !== null ){
                drag.classList.remove('show');
                drag.remove();
            }
            return this;
        }
    },
    /**
     * @type HTMLElement
     */
    'item': null,
    /**
     * @param {HTMLElement} item
     * @returns {CollectionView.Drag}
     */
    'capture': function( source ){
        if( this.ID === 0  && source !== null ){
            var id = source.getAttribute( 'data-id' );
            if( typeof id !== 'undefined' ){
                this.timer = window.setTimeout( function(){
                    if( source !== null ){
                        source.classList.add('captured');
                        if( source.parentNode !== null ){
                            source.parentNode.classList.add('stacking');
                        }
                    }
                    CollectionView.Drag.item = source;
                    CollectionView.Drag.ID = parseInt(id);
                    CollectionView.Drag.Icon.show();
                    console.log(CollectionView.Drag.ID + ' captured!');
                },this.elapsed );
                return true;
            }
        }
        return false;
    },
    /**
     * @param {HTMLElement} target 
     * @returns {boolean}
     */
    'stack': function( target  ){
        if( this.ID > 0  && typeof target !== 'undefined' ){
            var id = parseInt( target.getAttribute( 'data-id' ) || 0 );
            if( id !== this.ID ){
                this.release( true );
                return true;
            }
        }
        return false;
    },
    /**
     * @param {Boolean} remove 
     * @returns {CollectionView.Drag}
     */
    'release': function( remove ){
        if( this.ID ){
            console.log(this.ID + ' released!');
            this.ID = 0;
        }
        if( this.timer > 0 ){
            window.clearTimeout(this.timer);
            this.timer = 0;
        }
        if( this.item !== null ){
            this.item.classList.remove('captured');
            if( this.item.parentNode !== null ){
                this.item.parentNode.classList.remove('stacking');
            }
            if( remove === true ){
                this.item.remove();
            }
            this.item = null;
        }
        CollectionView.Drag.Icon.clear();
        return this;
    },
    /**
     * @returns {CollectionView.Drag}
     */
    'dragOut': function( target ){
        if( typeof target !== 'undefined' ){
            if( target.classList.contains('attached')){
                target.classList.remove('attached');
            }
        }
        return this;
    },
    /**
     * @param {HTMLElement} target
     * @returns {Number}
     */
    'dragOver':function ( target ) {
        if( this.ID > 0 && typeof target !== 'undefined' ){
            var id = parseInt( target.getAttribute('data-id') || 0 );
            if( id > 0 && id !== this.ID ){
                target.classList.add('attached');
                return id;
            }
        }
        return 0;
    }
};
/**
 * @param {Function} syncHandler 
 * @returns {CollectionModel}
 */
function CollectionModel( syncHandler ){
    
    var _client = {
        'self' : this,
        'queue':{
            'files':[],
            'current': 0
        },
        'debug':true
    };
    /**
     * @param {type} active
     */
    function sync( active ){ 
        if( typeof active !== 'boolean' ){ active = false; }
        console.log( active ? 'Sync On' : 'Sync Off');
    };

    var _sync = typeof syncHandler === 'function' ? syncHandler : sync;
    
    /**
     * @param {Object} data
     * @returns {CodersController}
     */
    function request( action , data , callback ){
        
        var content = {
            'ts': ( new Date( ) ).getMilliseconds( ),
            //wordpress ajax action caller
            'action': 'artpad_admin',
            //coders module action (controller.action)
            'request': action,
            'data': typeof data !== 'undefined' ? JSON.stringify(data) : false
        };
        
        _sync( true );
        
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.status >= 200 && this.status < 400) {
                if( this.readyState == 4 ){
                    if( typeof callback === 'function' ){
                        //console.log( this.responseText );
                        callback( JSON.parse( this.responseText ) );
                        _sync(false);
                    }
                    else if( _client.debug ){
                        console.log( this.responseText );
                    }
                    if( _client.debug ){
                        var ts = ( new Date( ) ).getMilliseconds( ) - content.ts;
                        console.log( 'Elapsed time ' + ts + 'ms' );
                    }
                }
            }
            else{
                if( _client.debug ){
                   //console.log( 'status: ' + this.status );
                }
            }
        };
        request.open('POST', _client.self.url( true ) , true );
        //required by WP_AJAX.PHP
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
        request.send( _client.self.serialize( content ) );

        //console.log( 'Requesting [ ' + action + ' ] with data ' + JSON.stringify( data ) + ' ...');
        //request.setRequestHeader("Content-type", "application/json;charset=UTF-8");
        //request.send( content );
        //request.send( JSON.stringify( content ) );
        return this;
    };
    /**
     * @returns {Boolean}
     */
    this.debug = () => _client.debug;
    /**
     * @param {Boolean} admin
     * @returns {String}
     */
    this.urlRoot = function( admin ){
        
        var url = window.location.pathname;
        
        return url.substr( 0 , url.indexOf( 'wp-admin/' ) );
        
        return url;
    };
    /**
     * @param {String} resource_id
     * @returns {String}
     */
    this.resourceURL = function( resource_id ){
        //return this.urlRoot() + '?artpad=rid.' + resource_id;
        return this.urlRoot() + 'artpad/rid.' + resource_id;
    };
    /**
     * @returns {String}
     */
    this.url = function( ){
        
        if( _client.debug ){
            //console.log( ajaxurl );
        }

        /*
         * defined in the admin header since version 2.8
         * /wp-admin/admin-ajax.php
         */
        return ajaxurl;

    };
    /**
     * @param {Object|Array} data
     * @returns {String}
     */
    this.serialize = function( data ){
        
        var serialized = [];
        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                serialized.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
            }
        }
        if(_client.debug ){
            //console.log( serialized );
        }
        return serialized.join('&');
    };
    /**
     * @param {String} action 
     * @param {Object} data
     * @returns {FormData}
     */
    this.formData = function( action , data ){
        
        var form = new FormData();

        for( var key in data ){
            if( data.hasOwnProperty( key ) ){
                form.append( key , data[ key ] );
            }
        }

        form.append('ts',( new Date( ) ).getMilliseconds( ));
        form.append('action','artpad_admin');
        form.append('_action',action);
        
        return form;
    };
    /**
     * @param {Boolean} mediaonly 
     * @returns {Array}
     */
    this.acceptedTypes = function( mediaonly ){
        
        if( typeof mediaonly === 'boolean' && mediaonly ){
            return [
                'image/png',
                'image/gif',
                'image/jpeg',
                'image/bmp',
            ];
        }
        
        return [
            'image/png',
            'image/gif',
            'image/jpeg',
            'image/bmp',
            'text/plain',
            'text/html',
            'text/json',
            'application/json'
        ];
    };
    /**
     * @param {type} type
     * @returns {Boolean}
     */
    this.isImage = function( type ){
        
        return typeof type === 'string' && type.indexOf('image/') === 0;
    };
    /**
     * @param {type} type
     * @returns {Boolean}
     */
    this.isDocument = function( type ){
        
        return typeof type === 'string' && type.indexOf('text/') === 0;
    };
    /**
     * @param {Array} files
     * @returns {CollectionModel}
     */
    this.upload = function( files ){
        if( _client.debug ){
            //console.log( files );
        }
        _client.queue.files = files;
        _client.queue.current = 0;
        return this.enqueueUpload( files );
    };
    /**
     * @param {Number} size
     * @param {Function} handler
     * @returns {CollectionModel}
     */
    this.grid = function( size , handler ){
        
        if( typeof size === 'undefined' ){ size = 0; }
        
        if( parseInt( size ) > 0 ){
            //set selected size
            request( 'set_grid' , {'size':size} , function( response ){
                console.log( 'grid updated!');
                if( typeof handler === 'function' ){
                    handler( parseInt( response.data ) );
                }
                else if( _client.debug ){
                    console.log( response );
                }
            });
        }
        else{
            //set selected size
            request( 'get_grid' , { } , function( response ){
                if( _client.debug ){
                    console.log( 'Getting grid size...' );
                    console.log( response );
                }
                if( typeof handler === 'function' ){
                    handler( response.data );
                }
            });
        }
        return this;
    };
    /**
     * 
     * @param {Number} id
     * @param {Function} handler
     * @returns {CollectionModel}
     */
    this.up = function( id , handler){
        request( 'attach' , {'ID':id } , function( response ){
            if( _client.debug ){
                console.log( response );
            }
            if( parseInt( response.data ) > 0 ){
                //handler( attach_id , to_id );
                handler();
            }
            else{
                console.log( 'Failed to move up ' + id );
            }
        } );
        return this;
    },
    /**
     * Attach a resource under a parent resource by ID
     * @param {Number} attach_id
     * @param {Number} to_id
     * @param {Function} handler
     * @returns {CollectionModel}
     */
    this.attach = function( attach_id , to_id , handler ){
        request( 'attach' , {'ID':attach_id,'parent_id' : to_id } , function( response ){
            if( _client.debug ){
                console.log( response );
            }
            if( parseInt( response.data ) > 0 ){
                //handler( attach_id , to_id );
                handler();
            }
            else{
                console.log( 'Failed to attach ' + attach_id + ' to ' + to_id );
            }
        } );
        return this;
    };
    /**
     * @param {String|Number} ID
     * @param {Function} handler
     * @returns {CollectionModel}
     */
    this.remove = function( ID , handler ){
        
        if( _client.debug ){
            console.log( 'Removing ' + ID + ' ...' );
        }
        
        if( typeof handler === 'function' ){
            request( 'remove' , {'ID' : ID } , function( response ){
                if( _client.debug ){
                    console.log( response );
                }
                //if( parseInt( response.data ) === 1 ){
                    //fire remove handler
                    handler( ID );
                //}
            });
        }
        
        return this;
    };
    /**
     * @param {Function} handler
     * @param {Number} id
     * @returns {CollectionModel}
     */
    this.item = function( handler , id ){
        //console.log( typeof handler );
        if( typeof handler === 'function' ){
            if( typeof id !== 'number' ){
                id = 0;
            }
            request( 'item' , {'id' : id } , function( response ){
                handler( response.data );
            } );
        }
        return this;
    };
    /**
     * @param {Function} handler
     * @param {String} post
     * @returns {CollectionModel}
     */
    this.collection = function( handler , id ){
        //console.log( typeof handler );
        if( typeof handler === 'function' ){
            if( typeof id !== 'number' ){
                id = 0;
            }
            request( 'collection' , {'id' : id } , function( response ){
                handler( Array.isArray( response.data ) ?
                            response.data :
                            Object.values(response.data) ,
                    id );
            } );
        }
        return this;
    };
    /**
     * @param {Function} handler
     * @param {Number} id
     * @returns {CollectionModel}
     */
    this.path = function( handler , id ){
        //console.log( 'Path finding ...' );
        if( typeof handler === 'function' ){
            if( typeof id !== 'number' ){
                id = 0;
            }
            request( 'path' , {'id' : id } , function( response ){
                handler( response.data , id );
            } );
        }
        return this;
    };



    /**
     * @param {File} fileData
     * @param {Function} fileHandle 
     * @returns {CodersController}
     */
    this.transfer = function( fileData , fileHandle ){
        
        console.log( typeof fileHandle );
        if( typeof fileHandle !== 'function' ){
            //console.log( 'No handler defined' );
            //return this;
        }
        
        var _self = this;

        var formData = new FormData();
        
        formData.append('upload', fileData);
        formData.append('action','artpad_admin');
        formData.append('_action','upload');

        var url = this.url();
        //console.log( 'Uploading ' + JSON.stringify( fileData.name ) + ' to ' + url );
        fetch( url , { method: 'POST', body: formData } )
            .then( (response) => response.json( ) )
            .then(function(data){
                if( !data.hasOwnProperty('error')){
                    if( Array.isArray( data ) ){
                        data.forEach( function(item){
                            _self.receive( item );
                        });
                        _self.enqueueUpload( fileHandle );
                    }
                    else{
                        _self.receive( data ).enqueueUpload( fileHandle );
                    }
                }
                else{
                    //_self.receive( ).enqueueUpload( fileHandle );
                    console.error( data );
                }
            }).catch( function( error ){
                if( _client.debug ){
                    console.log(error);
                }
            });

        return this;
    };
    /**
     * @param {Function} fileHandle 
     * @returns {CodersController}
     */
    this.enqueueUpload = function( fileHandle ){
        
        var file = this.nextFile();
        if( file !== false ){
            //call event for next upload
            this.transfer( file , fileHandle );
        }
        else{
            //this.closeUploader();
            console.log('Done!');
        }

        return this;
    };
    /**
     * @param {Object} progress
     * @returns {CodersController}
     */
    this.receive = function( fileData ){
        
        if( _client.debug ){
            console.log( 'Response Received' );
            console.log( fileData );
            return this;
        }
        
        if( fileData ){
            //publish file data into collection
            //console.log( item );
            this.repoContainer().appendChild( this.addItem( fileData ) );
            
            if( _client.debug ){
                console.log( 'File received: ' + fileData.name );
            }
        }
        else{
            //tag error
        }

        _client.queue.current++;

        //update progress bar
        return this.updateProgressBar( _client.queue.current / _client.queue.files.length );
    };
    /**
     * @returns {File|Boolean}
     */
    this.nextFile = function(){
        if( _client.queue.files.length > _client.queue.current ){
            var file = _client.queue.files[ _client.queue.current ];
            _client.queue.current++;
            return file;
        }
        return false;
    };
    /**
     * @param {File[]} fileCount
     * @returns {CodersController}
     */
    this.resetQueue = function( fileList ){
        _client.queue.files = fileList;
        _client.queue.current = 0;
        
        if( _client.debug ){
            console.log('Queuing ' + _client.queue.files.length + ' files ...' );
        }

        return this;
    };
    /**
     * @param {String} input
     * @returns {String}
     */
    this.cleanFileName = function( input ){

        var filename = input.split('\\');
        
        return filename[ filename.length - 1 ];
    };

    return this;
}
/**
 * https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
 */
(function( ){
    document.addEventListener('DOMContentLoaded', function (e) {
        //console.log('OK');
        new CollectionView( );
    });
})( /* autosetup */ );

