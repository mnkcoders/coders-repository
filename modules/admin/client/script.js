/**
 * 
 * @returns {CodersView}
 */
function CodersView( ){
    
    var _elements = {
        'dropZone': null,
        /**
         * @type Element
         */
        'tabs':null,
        /**
         * @type Element
         */
        'collectionBox':null,
        'timeout': 2000,
        'inputs':{
            'dropzone':'coders-repo-dropzone',
            'uploader':'coders-repo-uploader'
        }
        //'uploader':null
    };
    /**
     * 
     * @type CodersView
     */
    var _view = this;

    /**
     * @type Object
     */
    var _draggable = {
        'ID':0,
        'candrop':false,
        'moving':false,
        'reset': function(){
            this.ID = 0;
            this.candrop = false;
            this.moving = false;
            return this;
        }
    };
    /**
     * @type CodersModel
     */
    var _server = new CodersModel();
    
    var _self = this;

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
     * 
     * @param {Array} resources
     * @param {Number} parent_id 
     * @returns {CodersView}
     */
    this.displayCollection = function( resources , parent_id ){

        var container = _view.getContainer('collection');

        if( container !== false ){
            container.clear();
            container.setAttribute('data-id',parent_id);
            //console.log( resources );
            for( var r = 0 ; r < resources.length ; r++ ){
                if( r % 4 === 0 ){
                    _self.wait();
                }
                container.appendChild( _self.addItem( resources[ r ] ) );
            }
        }
        
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
     * @param {String} message 
     * @returns {CodersController}
     */
    this.notify = function( message ){
        
        document.querySelectorAll('');
        
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
     * li.item
     *      div.content
     *              a.type[file-type]
     *                  img alt title
     *                  span[name]
     * @param {Object} itemData
     * @returns {Element}
     */
    this.addItem = function( itemData ){

        var titleClass = ['action','center','cover','title'];
        if( _server.isDocument( itemData.type )){
            titleClass.push('dashicons dashicons-text');
        }

        var elements = [
            this.element('span',{'class': titleClass.join(' ') } , itemData.name ),
            this.element('a',{
                'class':'action open dashicons dashicons-admin-links top-right rounded',
                'target':'_blank',
                'title':'Display',
                'href': _server.resourceURL(itemData.public_id)}),
            this.element('a',{
                'class':'action remove dashicons dashicons-trash bottom-left rounded',
                'target':'_self',
                'title':'Remove',
                'href':_server.url({'task':'remove','id':itemData.ID})})
        ];

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
            this.element('div',{'class':'content'},elements) );

        ['drag','dragenter','dragleave','dragover','drop'].forEach( function( event ){
            item.addEventListener( event , function(e){
                e.preventDefault();
                e.stopPropagation();
                var key = 'text/plain';
                switch( event ){
                    case 'dragstart':
                    case 'dragenter':
                        //drag(event)
                        //e.dataTransfer.setData( key, ID );
                        _draggable.ID = this.getAttribute('data-id');
                        _draggable.candrop = false;
                        _draggable.moving = true;
                        console.log( 'Moving item [ ' + _draggable.ID + ' ]' );
                        return true;
                    case 'dragleave':
                        var target_id = this.getAttribute('data-id');
                        _draggable.candrop = false;
                        console.log( 'Leaving [ ' + target_id  + ' ] ...');
                        return true;
                    case 'dragover':
                        var target_id = this.getAttribute('data-id');
                        if( _draggable.ID !== target_id ){
                            _draggable.candrop = true;
                            //var child_id = e.dataTransfer.getData(key);
                            var source_id = _draggable.ID;
                            console.log( '[ ' + source_id + ' ] is over [ ' + target_id + ' ] ...' );
                            return true;
                        }
                        else{
                            _draggable.candrop = false;
                        }
                        return false;
                    case 'drop':
                        //var child_id = e.dataTransfer.getData(key);
                        if( _draggable.candrop ){
                            var target_id = this.getAttribute('data-id');
                            var source_id = _draggable.ID;
                            console.log( 'Dropping [ ' + source_id  + ' ] over [ ' + target_id + ' ] ...');
                            _draggable.reset();
                        }
                        return true;
                }
            });
        });


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
     * @returns {Element}
     */
    this.uploader = function(  ){

        var inputFileSize = this.element('input',{'type':'hidden',
                'name':'MAX_FILE_SIZE',
                'value':CodersView.FileSize()});
        var inputFiles = this.element('input',{
                'class':'hidden',
                'id': 'id_uploader',
                'type':'file',
                'name':'upload[]',
                'multiple':true,
                //'accept':this.acceptedTypes().join(', '),
                //'id': _repo.inputs.dropzone + '_input'
            });
        var inputButton = this.element('button',{
                'class':'button button-primary dashicons-before dashicons-upload',
                'type':'submit',
                'name':'_action',
                'value':'default'
            }, 'Upload' );
            
        inputButton.addEventListener( 'click', function(e){
            //e.preventDefault();
            this.value = _self.selectedTab();
            console.log( this.name + ':' + this.value );
            return true;
        });
            
        //capture upload events
        inputFiles.addEventListener( 'change', function(e){
                var fileList = this.files;
                console.log( fileList );
                return true;
            });



        var url = 'http://localhost/WORDPRESS/artistpad/wp-admin/admin.php?page=coders-main&_action=admin.main.upload';
        
        //var container = _view.getContainer('collection');
        //var parent_id = container.getAttribute('data-id');
        //console.log( parent_id );
         
        var formData = this.element('form',{
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
        
        var uploader = this.element('div',{'class':'uploader item container' },[
            formData,
            this.element('label',{
                'class':'dashicons-before dashicons-media-default button',
                'for': 'id_collection',
            }, 'Select files' ),
            this.progressBar( 'Upload' , 'hidden content' )
        ]);
        
        uploader.addEventListener( 'click', e => {
                //e.preventDefault();
                e.stopPropagation();
                return false;
            });


        return uploader;
    };
    /**
     * @returns {Element}
     */
    this.renderPostForm = function(){
        
        //create here the post form
        var txtTitle = _view.element('h2',{'class':'title panel center'},'Post Name');
        var txtName = _view.element('input',{'type':'text','name':'name','id':'id_name'});
        var txtContent = _view.element('textarea',{'name':'content','id':'id_content'});
        var btnUpdate = _view.element('button',{'type':'submit','name':'_action','value':'save'});
        
        var form = _view.element('div',{'class':'container post-data hidden solid'},[
            txtTitle,
            txtName,
            txtContent,
            btnUpdate
        ]);
        
        
        return form;
    };
    /**
     * @param {String} cls
     * @returns {Element}
     */
    this.renderGridResizer = function( cls ){
        
        var options = [];
        
        [4,6,10].forEach( function( size ){
            var item = _view.element('li',{'class':'option button','data-size':size},'x' +  size.toString( ) );
            item.addEventListener('click',function(e){
                e.preventDefault();
                //console.log('Clicked ' + this.getAttribute('data-size') );
                var size = this.getAttribute('data-size');
                var collection = _view.getContainer('collection');
                var grid = 'grid-' + size.toString();
                if( !collection.classList.contains(grid) ){
                    collection.classList.remove('grid-4');
                    collection.classList.remove('grid-6');
                    collection.classList.remove('grid-10');
                    collection.classList.add(grid);
                }
            });
            options.push(item);
        });

        var resizer = _view.element('ul',{'class':'grid inline ' + cls}, options );
                        
        return resizer;
    };
    /**
     * @param {array} collections 
     * @returns {CodersView}
     */
    this.initialize = function( ){

        var container = _view.getContainer();
        
        if( null !== container ){
            
            container.appendChild( _view.element('div',{'class': 'toolbox container inline solid clearfix centered'},[
                _view.element('ul',{'class':'navigator panel left inline'},_view.element('li',{'class':'home'},'#Home')),
                _view.element('span',{'class':'panel title inline centered'},'Title'),
                
                this.renderGridResizer('panel right'),
                _view.element('span',{'class':'panel button right'},'Post Data')
            ]));

            container.appendChild( _view.renderPostForm() );
            container.appendChild( _view.uploader());
            container.appendChild( _view.element('ul', {'class': 'collection grid-10' }));

            _server.listResources( _view.displayCollection );
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
CodersView.FileSize = function(){ return 256 * 256 * 256; };
/**
 * @returns {CodersUploader}
 */
function CodersModel(){
    
    var _client = {
        'self' : this,
        'queue':{
            'files':[],
            'current': 0
        },
        'debug':true
    };
    
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
            '_action': action,
            'data': typeof data !== 'undefined' ? JSON.stringify(data) : false
        };
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.status >= 200 && this.status < 400) {
                if( this.readyState == 4 ){
                    if( typeof callback === 'function' ){
                        //console.log( this.responseText );
                        callback( JSON.parse( this.responseText ) );
                    }
                    else if( _client.debug ){
                        console.log( this.responseText );
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

        //request.setRequestHeader("Content-type", "application/json;charset=UTF-8");
        //request.send( content );
        //request.send( JSON.stringify( content ) );
        return this;
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
        return this.urlRoot() + '?resource=' + resource_id;
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
     * @returns {CodersModel}
     */
    this.upload = function( files ){
        console.log( files );
        _client.queue.files = files;
        _client.queue.current = 0;
        return this.enqueueUpload( files );
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
    
    this.collections = function(){
        
        return [];
    };
    /**
     * @param {Function} handler
     * @param {String} post
     * @returns {CodersModel}
     */
    this.listResources = function( handler , post_id ){
        //console.log( typeof handler );
        if( typeof handler === 'function' ){
            if( typeof post_id !== 'number' ){
                post_id = 0;
            }
            request( 'collection' , {'ID':post_id } , function( response ){
                //console.log( response );
                handler( Array.isArray( response.data ) ?
                            response.data :
                            Object.values(response.data) ,
                    post_id );
            } );
        }
        return this;
    };
    /**
     * @param {Function} callback
     * @returns {CodersModel}
     */
    this.listCollections = function( callback ){
            request( 'list_collections' , {} , function( response ){
                
                var collections = response.data || [];
                
                callback( collections );
            });
            return this;
    };
    /**
     * @param {String} collection
     * @param {Function} callback
     * @returns {CodersModel}
     */
    this.createCollection = function( collection  , callback ){
        
        request( 'create_collection', { 'collection': collection }, function( response ){
            
            //console.log( response );
            var collection = response.data.hasOwnProperty('collection') ?
                response.data.collection : '';
            
            callback( collection );
        });
        
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
    this.remove = function( resource ){
        
        return false;
    };
    
    this.dropCollection = function( collection ){
        
        return false;
    };
    
    return this;
}

/**
 * https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
 */
(function CodersController( ){
    
    var _repo = {
        /**
         * @type CodersView
         */
        //'view': new CodersView(),
        'view': null,
        /**
         * @type CodersModel
         */
        //'server': new CodersModel(),
        'debug': true
    };
    /**
     * @returns {CodersController}
     */
    this.bind = function(){

        //var _controller = this;

        document.addEventListener('DOMContentLoaded',function(e){

            _repo.view = new CodersView( );
            
        });
                
        return this;
    };
    
    return this.bind(/*setup client*/);
})( /* autosetup */ );

