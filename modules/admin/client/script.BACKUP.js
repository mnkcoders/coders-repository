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
     * @returns {Element[]}
     */
    this.tabs = function(){
        return typeof _elements.tabs === 'object' ?
            [].slice.call( _elements.tabs.children ) :
                    [];
    };
    /**
     * @param {String} cls 
     * @returns {Element|Boolean}
     */
    this.firstTab = function( cls ){
        var tabs = this.tabs();
        if( tabs.length ){
            if( typeof cls !== 'undefined' ){
                tabs[ 0 ].classList.add( cls );    
            }
            return tabs[ 0 ];
        }
        return false;
    };
    /**
     * @param {String} selection
     * @returns {Element|Boolean}
     */
    this.collection = function( selection ){
        
        var container = this.panels( selection );
        //console.log( container.childNodes );
        
        for( var c = 0 ; c < container.childNodes.length ; c++ ){
            if( container.childNodes[ c ].classList.contains('collection') ){
                return container.childNodes[ c ];
            }
        }
        
        return false;
    };
    /**
     * @returns {String}
     */
    this.selectedTab = function(){
        var tabs = this.tabs();
        for( var t = 0 ; t < tabs.length ; t++ ){
            if( tabs[ t ].classList.contains('active')){
                return tabs[t].getAttribute('data-tab');
            }
        }
        return '';
    };
    /**
     * @param {String} selection 
     * @returns {Element[]|Boolean}
     */
    this.panels = function( selection ){
        
        if( typeof selection === 'string' && selection.length ){
            var panels = this.panels();
            for( var p = 0 ; p < panels.length ; p++ ){
                if( panels[p].getAttribute('data-tab') === selection ){
                    return panels[p];
                }
            }
        }
        
        return typeof _elements.collectionBox === 'object' ?
            [].slice.call( _elements.collectionBox.children ) :
                    [];
    };
    /**
     * @param {String} cls 
     * @returns {Element[]|Boolean}
     */
    this.firstPanel = function( cls ){
        var panels = this.panels();
        if( panels.length ){
            if( typeof cls !== 'undefined' ){
                panels[ 0 ].classList.add( cls );
            }
            return panels[ 0 ];
        }
        return false;
    };
    /**
     * @param {String} tab
     * @returns {CodersView}
     */
    this.switchTab = function( selection ){
        
        if( typeof selection === 'undefined' ){
            this.firstTab( 'active' );
            this.firstPanel( 'active' );
            return this;
        }
        
        this.tabs().forEach( function( tab ){
            if( tab.getAttribute('data-tab') === selection ){
                if( !tab.classList.contains('active')) {
                    tab.classList.add('active');
                }
            }
            else{
                tab.classList.remove('active');
            }
        });

        this.panels().forEach( function( panel ){
            if( panel.getAttribute('data-tab') === selection  && !panel.classList.contains('active')){
                if( !panel.classList.contains('active')) {
                    panel.classList.add('active');
                }
            }
            else{
                panel.classList.remove('active');
            }
        });
        
        if( selection !== 'create-collection' ){
            var _view = this;
            _server.listResources( selection , _view.listPosts );
        }
        
        return this;
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
     * @param {String} collection
     * @param {Array} resources
     * @returns {CodersView}
     */
    this.listPosts = function( collection , resources ){
        
        var panel = _self.panels( collection );
                
        if( panel.hasOwnProperty('element') ){
            var gallery = panel.element('collection');
            //var panel = _self.collection( collection );
            if( gallery !== false ){
                gallery.clear();
            }
            for( var r = 0 ; r < resources.length ; r++ ){
                if( r % 4 === 0 ){
                    _self.wait();
                }
                gallery.appendChild( _self.addItem( resources[ r ] ) );
            }
            //resources.forEach( function( r ){
            //    gallery.appendChild( _self.addItem( r ) );
            //});
        }
        return this;
    };
    /**
     * @param {String} collection
     * @param {String} title
     * @returns {CodersView}
     */
    this.addCollection = function( collection , project_title ){

        var cls = ['inline','collection',collection];
        
        return this.addPanel( collection , [
            //this.appendUploader( collection ) ,
            this.element('ul', {'class': cls.join(' ') }) ], project_title );
    };
    /**
     * @param {String} collection
     * @param {Element} panel
     * @param {String} title
     * @returns {CodersView}
     */
    this.addPanel = function( item , panel , title ){

        var _view = this;

        var cls = item === 'create-collection' ?
                'item create button button-primary' :
                'item button';
        if( typeof title === 'undefined' ){
            title = item;
        }
        //var title = item === 'create-collection' ?
        //        'New Collection' :
        //                item;

        var tab = this.element('li',{'class':cls,'data-tab':item},title );
        
        tab.addEventListener( 'click' , e => {
            e.preventDefault();
            e.stopPropagation();
            _view.switchTab( item );
            console.log('clicked ' + item );
            return false;
        } );

        _elements.tabs.prepend( tab );
        
        _elements.collectionBox.prepend(this.element('div',{
            'class':'tab', 'data-tab':item
        }, panel ) );
        
        return this;
    };
    
    this.getContainer = function(){
        return document.getElementById( 'repository' );
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

        var titleClass = ['action','center','cover'];
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
     * @param {String} collection 
     * @returns {Element}
     */
    this.uploader = function( collection ){

        var inputFileSize = this.element('input',{'type':'hidden',
                'name':'MAX_FILE_SIZE',
                'value':CodersView.FileSize()});
        var inputFiles = this.element('input',{
                'class':'hidden',
                'id': collection + '-files',
                'type':'file',
                'name':'upload[]',
                'multiple':true,
                //'accept':this.acceptedTypes().join(', '),
                //'id': _repo.inputs.dropzone + '_input'
            });
        var inputButton = this.element('button',{
                'class':'button button-primary dashicons-before dashicons-upload',
                'id': ( collection + '-upload' ),
                'type':'submit',
                'name':'collection',
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
         
        var formData = this.element('form',{
            //FORM DECLARATION
            'name': 'collection',
            'method':'POST',
            //'action': _server.url(),
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
                'for': ( collection + '-files' )
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
     * @param {String} collection 
     * @returns {Element}
     */
    this.appendUploader = function( collection ){

        //handle here the progressBar to attach a caller when required
        var progressBar = this.progressBar( 'Upload' , 'hidden content' );
        
        var inputFileSize = this.element('input',{'type':'hidden',
                'name':'MAX_FILE_SIZE',
                'value':CodersView.FileSize()});
        var inputFiles = this.element('input',{
                'class':'hidden',
                'id': collection + '-files',
                'type':'file',
                'name':'upload[]',
                'multiple':true,
                //'accept':this.acceptedTypes().join(', '),
                //'id': _repo.inputs.dropzone + '_input'
            });
        var inputButton = this.element('button',{
                'class':'button button-large',
                'id': ( collection + '-upload' ),
                'type':'submit',
                'name':'action',
                'value':'main.upload'
            }, 'Upload' );
            
        var url = 'http://localhost/WORDPRESS/artistpad/wp-admin/admin.php?page=coders-main&_action=admin.main.upload&collection=' + collection;
         
        var formData = this.element('form',{
            //FORM DECLARATION
            'name': 'collection',
            'method':'POST',
            //'action': _server.url(),
            'action': url,
            'enctype':'multipart/form-data'
        },[
            //FORM ELEMENTS
            inputFileSize,
            inputFiles,
            inputButton
        ]);
        //console.log( formData.element( 'button' ) );
        //var files = [];
        formData.addEventListener( 'change', e => {
            //e.preventDefault();
            //progressBar.setLabel('Uploading...');
            //console.log(e);
            //_server.upload( files , function( response ){
            //    console.log( response );
            //});
            //avoid bubbling over form
            return true;
        });
        
        var dropZone = this.element('div',{'class':'uploader item container' },[
            formData,
            this.element('label',{
                'class':'dashicons-before dashicons-upload button button-primary',
                'for': ( collection + '-files' )
            }, 'Upload' ),
            progressBar

        ]);
        
        dropZone.addEventListener( 'click', e => {
                //e.preventDefault();
                e.stopPropagation();
                return false;
            });

        //capture upload events
        inputFiles.addEventListener( 'change', function(e){
                return true;
                dropZone.classList.add('uploading');
                //pBarContainer.classList.add('current');
                console.log('Selecting files...');
                var fileList = this.files;
                if ( typeof fileList !== 'undefined' && fileList.length ) {
                    progressBar.setLabel('Uploading ...');
                    var collection = _self.selectedTab();
                    _server.upload( fileList , function( response ){
                        //get all registered file metadata to append
                        //them into the collection
                        console.log( response ) ;
                    });
                    return true;
                }
                else{
                    progressBar.setLabel('No files to upload?');
                }
                return false;
            });

        ['dragenter','dragleave','dragover','drop'].forEach( function( event ){
            dropZone.addEventListener(event, function(e){
                e.preventDefault();
                e.stopPropagation();
                switch( event ){
                    case 'dragenter':
                    case 'dragover':
                        dropZone.classList.add('highlight');
                        break;
                    case 'dragleave':
                        dropZone.classList.remove('highlight');
                        break;
                    case 'drop':
                        dropZone.classList.add('uploading');
                        //pBarContainer.classList.add('current');
                        var files = e.dataTransfer.files;
                        if (files.length) {
                            progressBar.setLabel('Uploading ...');
                            var collection = _self.selectedTab();
                            _server.upload( files , function( response ){
                                //get all registered file metadata to append
                                //them into the collection
                                console.log( response ) ;
                            });
                        }
                        else{
                            progressBar.setLabel('No files to upload?');
                        }
                        break;
                }
            }, false);
        });

        return dropZone;
    };
    /**
     * @param {array} collections 
     * @returns {CodersView}
     */
    this.initialize = function( ){
        
        var container = this.getContainer();
        
        if( null !== container ){

            _elements.tabs = this.element('ul',{
                'class':'collection-tab inline container'
            });
            _elements.collectionBox = this.element('div',{
                //'class': 'repository-box grid-4'
                'class': 'repository-box grid-6'
            } );

            container.appendChild( _elements.tabs );
            container.appendChild( this.element('div',{'class':'toolbox'},'#toolbox'));
            container.appendChild( this.uploader());
            container.appendChild( _elements.collectionBox );
            
            var txtCollection = this.element('input',{
                'type': 'text',
                'name': 'collection',
                'placeholder': 'Name your collection'});
            var btnCollection = this.element('button',{
                'class': 'button button-primary',
                'type': 'submit',
                'name': 'action',
                'value': 'create'},
            'Create');
            
            btnCollection.addEventListener('click', e => {
                e.preventDefault();
                var collection = txtCollection.value;
                console.log('Create collection ' + collection );
                _server.createCollection( collection , _view.addCollection );
                return true;
            });

            this.addPanel('create-collection', this.element('div',
                    {'class': 'content', 'data-tab': 'create-collection'}, [
                    txtCollection, btnCollection
                    ] ) , 'New Collection' );
            
            var _view = this;
            _server.listCollections( function( collections ){
                //console.log( collections );
                switch( true ){
                    case typeof collections === 'object':
                        for( var c in collections ){
                            if( collections.hasOwnProperty( c ) ){
                                console.log( c + ':' + collections[ c ] );
                                _view.addCollection( c , collections[ c ] );
                            }
                        }
                        _view.switchTab( );
                        break;
                    case Array.isArray( collections ):
                        collections.forEach( function( item ){
                            //console.log( collection );
                            _view.addCollection( item );
                        });
                        _view.switchTab( );
                        break;
                }
            } );
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
        'queue':{
            'files':[],
            'current': 0
        },
        'debug':true
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
        form.append('action','coders_admin');
        form.append('_action',action);
        
        return form;
    };
    /**
     * @param {Object} data
     * @returns {CodersController}
     */
    this.ajax = function( action , data , callback ){
        
        var content = {
            'ts': ( new Date( ) ).getMilliseconds( ),
            //wordpress ajax action caller
            'action': 'coders_admin',
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
        request.open('POST', this.url( true ) , true );
        //required by WP_AJAX.PHP
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
        request.send( this.serialize( content ) );

        //request.setRequestHeader("Content-type", "application/json;charset=UTF-8");
        //request.send( content );
        //request.send( JSON.stringify( content ) );
        return this;
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
        formData.append('action','coders_admin');
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
     * @param {String} collection
     * @param {Function} handler
     * @returns {CodersModel}
     */
    this.listResources = function( collection , handler ){
        //console.log( typeof handler );
        if( typeof handler === 'function' ){
            return this.ajax( 'collection' , {'collection':collection } , function( response ){
                //console.log( response );
                handler( collection , response.data );

            } );
        }
    };
    /**
     * @param {Function} callback
     * @returns {CodersModel}
     */
    this.listCollections = function( callback ){
            this.ajax( 'list_collections' , {} , function( response ){
                
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
        
        this.ajax( 'create_collection', { 'collection': collection }, function( response ){
            
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
    /**
     * @returns {CodersModel}
     */
    this.initialize = function(){
        //testing url
        //console.log( this.url( ) );
        //this.ajax( 'default' , {'message':'Hello!'} , function( response ){
        //    console.log(response);
        //});
        return this;
    };
    
    return this.initialize();
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

