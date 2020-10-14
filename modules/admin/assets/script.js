/**
 * 
 * @returns {CodersView}
 */
function CodersView(){
    
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
        },
        //'uploader':null
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
     * @returns {Element[]}
     */
    this.panels = function(){
        return typeof _elements.collectionBox === 'object' ?
            [].slice.call( _elements.collectionBox.children ) :
                    [];
    };
    /**
     * @param {String} tab
     * @returns {CodersView}
     */
    this.switchTab = function( selection ){
        
        console.log(this.tabs());
        this.tabs().forEach( function( tab ){
            if( tab.getAttribute('data-tab') === selection  && !tab.classList.contains('active')){
                tab.classList.add('active');
            }
            else{
                tab.classList.remove('active');
            }
        });

        this.panels().forEach( function( panel ){
            if( panel.getAttribute('data-tab') === selection  && !panel.classList.contains('active')){
                panel.classList.add('active');
            }
            else{
                panel.classList.remove('active');
            }
        });
        
        return this;
    };
    /**
     * @param {String} collection
     * @param {Element} panel
     * @returns {CodersView}
     */
    this.addPanel = function( item , panel ){

        var _view = this;

        var cls = item === 'create-collection' ?
                'item create icon-plus button-primary' :
                'item button';
        var title = item === 'create-collection' ?
                'New Collection' :
                        item;

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
            'class':'container', 'data-tab':item
        }, panel ) );
        
        return this;
    };
    
    /**
     * @returns {String}
     */
    this.url = function( vars , root ){
        
        var url = window.location.href;
        
        if( root ){
            url = url.substr( 0 , url.indexOf('/coders-repository/'));
        }
        
        if( typeof vars === 'object' ){

            var params = [];
            
            Object.keys( vars ).forEach(function(item){
            
                params.push( item + '=' + vars[ item ] );
            });
            
            return url +
                ( url.indexOf('?') > -1 ? '&' : '?' ) +
                params.join('&');
        }
        
        //return _repo.URL + '?page=coders-repository';
        return url;
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
        
        return e;
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

        if( this.acceptedTypes().includes( itemData.type ) ){
            //var img = document.createElement('img');
            var img = this.image({
                'alt':itemData.name,
                'title':itemData.name,
                'src':this.url({'resource_id':itemData.ID},true)
            });
            
        }
        
        var caption = this.element('span',false,itemData.name);
        var link = this.element('a',{'className':'action open icon-link'});
        var remove = this.element('a',{
            'className':'action remove icon-remove',
            'href':this.url({'task':'remove','id':itemData.ID})});

        var content = this.element('div',{'className':'content'});
        content.appendChild(img);
        content.appendChild(caption);
        content.appendChild(link);
        content.appendChild(remove);

        var item = this.element('li',{'className':'item'});
        //item.href = itemData.url;
        item.appendChild(content);
        
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
     * @param {Function} uploadHandler Upload exchange handler
     * @returns {Element}
     */
    this.uploader = function( collection , uploadHandler ){
      
        var _view = this;
       
        //handle here the progressBar to attach a caller when required
        var progressBar = this.progressBar('Upload','hidden content');

        var formData = this.element('form',{
            //FORM DECLARATION
            'name': 'collection',
            'method':'POST',
            'action':this.url({'action':'upload'}),
            'enctype':'multipart/form-data'
        },[
            //FORM ELEMENTS
            this.element('input',{'type':'hidden',
                'name':'MAX_FILE_SIZE',
                'value':CodersView.FileSize()}),
            this.element('input',{
                'class':'hidden',
                'id': collection + '-files',
                'type':'file',
                'name':'upload',
                'multiple':true,
                //'accept':this.acceptedTypes().join(', '),
                //'id': _repo.inputs.dropzone + '_input'
            }).addEventListener( 'click', e => {
                console.log('Selecting files...');
                console.log(e);
                return true;
            })
            /*this.element('button',{
                'class':'button button-large icon-upload hidden',
                'id': ( collection + '-upload' ),
                'type':'submit',
                'name':'action',
                'value':'upload'
            }, progressBar )*/
        ]);
        
        formData.addEventListener( 'change', e => {
            e.preventDefault();
            progressBar.setLabel('Uploading...');
            console.log(e);
            uploadHandler( [] , progressBar );
            //avoid bubbling over form
            return true;
        });
        
        var dropZone = this.element('li',{'class':'uploader item'},[
            formData,
            this.element('label',{
                'class':'icon-upload large-icon content',
                'for': ( collection + '-files' )
            }, 'Upload' ),
            progressBar

        ]);
           
        dropZone.addEventListener( 'click', e => {
                //e.preventDefault();
                e.stopPropagation();
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
                        dropZone.classList.remove('uploading');
                        //pBarContainer.classList.add('current');
                        if( typeof uploadHandler === 'function' ){
                            var files = e.dataTransfer.files;
                            if( files.length ){
                                progressBar.setLabel('Uploading ...');
                                uploadHandler( files , progressBar );
                            }
                        }
                        else{
                            progressBar.setLabel('Invalid Upload Handler');
                        }
                        break;
                }
            }, false);
        });

        return dropZone;
    };
    /**
     * @returns {CodersView}
     */
    this.initialize = function(){
        
        var container = this.getContainer();
        if( null !== container ){
            _elements.tabs = this.element('ul',{
                'class':'collection-tab inline container'
            });
            _elements.collectionBox = this.element('div',{
                'class': 'repository-box'
            } );

            container.appendChild( _elements.tabs );
            container.appendChild( _elements.collectionBox );
            
            this.addPanel( 'create-collection' , this.element('div',
                {'class':'content','data-tab':'create-collection'},[
                this.element('input',
                    {'type':'text','name':'collection','placeholder':'Name your collection'}),
                this.element('button',
                    {'type':'submit','name':'action','value':'create'},'Create')
            ]));
            this.addPanel( 'test1' , this.element('ul',
                {'class':'collection test1 inline'},
                this.uploader('test1',function(){})));
            this.addPanel( 'test2' , this.element('ul',
                {'class':'collection test2 inline'},
                this.uploader('test2',function(){})));
            this.addPanel( 'test3' , this.element('ul',
                {'class':'collection test3 inline'},
                this.uploader('test3',function(){})));
        }
        else{
            console.log('Container not found');
        }
        return this;
    };
    
    return this.initialize();
}
/**
 * @returns {Number}
 */
CodersView.FileSize = function(){ return 256 * 256 * 256; };
/**
 * @returns {CodersUploader}
 */
function CodersModel(){
   
    /**
     * @returns {String}
     */
    this.urlRoot = function(){
        return window.location.pathname;
    };
    /**
     * @returns {String}
     */
    this.url = function( vars , root ){
        
        var url = window.location.href;
        
        if( root ){
            url = url.substr( 0 , url.indexOf('/coders-repository/'));
        }
        
        if( typeof vars === 'object' ){

            var params = [];
            
            Object.keys( vars ).forEach(function(item){
            
                params.push( item + '=' + vars[ item ] );
            });
            
            return url +
                ( url.indexOf('?') > -1 ? '&' : '?' ) +
                params.join('&');
        }
        
        //return _repo.URL + '?page=coders-repository';
        return url;
    };
    
    
    return this;
}

/**
 * https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
 */
(function CodersController( ){
    
    var _repo = {
        'collections':[],
        /**
         * @type CodersView
         */
        'view': null,
        /**
         * @type CodersModel
         */
        'server': new CodersModel(),
        'debug': true
    };
    /**
     * @returns {String}
     */
    this.url = function( vars , root ){
        
        var url = window.location.href;
        
        if( root ){
            url = url.substr( 0 , url.indexOf('/coders-repository/'));
        }
        
        if( typeof vars === 'object' ){

            var params = [];
            
            Object.keys( vars ).forEach(function(item){
            
                params.push( item + '=' + vars[ item ] );
            });
            
            return url +
                ( url.indexOf('?') > -1 ? '&' : '?' ) +
                params.join('&');
        }
        
        //return _repo.URL + '?page=coders-repository';
        return url;
    };
    /**
     * @returns {String}
     */
    this.urlRoot = function(){
        return window.location.pathname;
    };
    /**
     * @returns {CodersController}
     */
    this.server = function(){
        
        return this;
    };
    /**
     * @returns {CodersController}
     */
    this.createCollection = function( collection ){
        
        return this;
    };
    /**
     * @returns {CodersController}
     */
    this.collections = function(){
        
        var _self = this;
        
        return this.ajax( 'collections' , {} , function( collections ){
            
            //clear up the current view (shitty JS option)
            _self.collectionsContainer().innerHTML = '';
            //foreach resource, append into the current list view
            for( var c = 0 ; c < collections.length ; c++ ){
                //_self.collectionsContainer().appendChild();
            }
            
            if( _repo.debug ){
                console.log( typeof resources );
            }
        });
    };
    /**
     * @param {String|Array} filters
     * @returns {CodersController}
     */
    this.list = function( filters ){
        
        var _self = this;
        
        if( typeof filters === 'undefined' ){
            filters = false;
        }

        if( _repo.debug ){
            console.log('Loading contents...');
        }
 
        return this.ajax( 'list' , {'filters':filters} , function( resources ){
            
            //clear up the current view (shitty JS option)
            _self.repoContainer().innerHTML = '';
            _self.repoContainer().appendChild( _self.appendUploadButton(  ) );
            //foreach resource, append into the current list view
            for( var id in resources ){
                if( resources.hasOwnProperty( id ) ){
                    //console.log( resources[id] );
                    _self.repoContainer().appendChild(  _self.addItem( resources[id] ) );
                }
            }
            
            if( _repo.debug ){
                console.log( typeof resources );
            }
        });
    };
    /**
     * data.task
     * data.callback
     * @param {Object} data
     * @returns {CodersController}
     */
    this.ajax = function( task , data , callback ){

        var content = {
            'ts': ( new Date( ) ).getMilliseconds( ),
            'status': 1,
            'data': typeof data !== 'undefined' ? JSON.stringify(data) : false
        };

        var url = this.url({'task':task});

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if ( this.status == 200 ) {
                if( this.readyState == 4 ){
                    if( typeof callback === 'function' ){
                        callback( JSON.parse( this.responseText ) );
                    }
                    //console.log( this.responseText );
                }
            }
            else{
                if( _repo.debug ){
                   console.log( 'status: ' + this.status );
                }
            }
        };
        xhttp.open('POST', url , true );
        xhttp.setRequestHeader("Content-type", "application/json");
        xhttp.send( content );
        
        if( _repo.debug ){
            console.log( url );
        }

        return this;
    };
    /**
     * @param {File} fileData
     * @returns {CodersController}
     */
    this.transfer = function( fileData ){
        
        var _controller = this;

        var formData = new FormData();
        
        formData.append('upload', fileData);

        var url = this.url({'task':'dragDrop'});
        //console.log( 'Uploading ' + JSON.stringify( fileData.name ) + ' to ' + url );
        fetch( url , { method: 'POST', body: formData } )
            .then( (response) => response.json( ) )
            .then(function(data){
                if( !data.hasOwnProperty('error')){
                    if( Array.isArray( data ) ){
                        data.forEach( function(item){
                            _controller.receive( item );
                        });
                        _controller.attemptUpload();
                    }
                    else{
                        _controller.receive( data ).attemptUpload();
                    }
                }
                else{
                    _controller.receive( ).attemptUpload();
                }
            }).catch( function( error ){
                if( _repo.debug ){
                    console.log(error);
                }
            });

        return this;
    };
    /**
     * @param {String} id
     * @returns {String}
     */
    this.itemUrl = function( id ){
        return this.url( {'resource_id':id} );
    };
    /**
     * @param {Object} progress
     * @returns {CodersController}
     */
    this.receive = function( fileData ){
        
        if( fileData ){
            //publish file data into collection
            //console.log( item );
            this.repoContainer().appendChild( this.addItem( fileData ) );
            
            if( _repo.debug ){
                console.log( 'File received: ' + fileData.name );
            }
        }
        else{
            //tag error
        }

        _repo.queue.current++;

        //update progress bar
        return this.updateProgressBar( _repo.queue.current / _repo.queue.files.length );
    };
    /**
     * @returns {File|Boolean}
     */
    this.currentFile = function(){
        return _repo.queue.files.length > _repo.queue.current ?
                _repo.queue.files[ _repo.queue.current ] :
                        false;
    };
    /**
     * @returns {CodersController}
     */
    this.attemptUpload = function( ){
        
        var upload = _repo.queue.files[ _repo.queue.current ];
        
        if( _repo.queue.current < _repo.queue.files.length ){
            //call event for next upload

            this.transfer( upload );
        }
        else{
            this.closeUploader();
        }

        return this;
    };
    /**
     * @param {File[]} fileCount
     * @returns {CodersController}
     */
    this.resetQueue = function( fileList ){
        _repo.queue.files = fileList;
        _repo.queue.current = 0;
        
        if( _repo.debug ){
            console.log('Queuing ' + _repo.queue.files.length + ' files ...' );
        }

        return this;
    };
    /**
     * @param {String} collection
     * @returns {CodersController}
     */
    this.loadCollection = function( collection ){
        
        return this;
    };
    /**
     * @returns {Array}
     */
    this.acceptedTypes = function(){
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
     * @param {String} input
     * @returns {String}
     */
    this.cleanFileName = function( input ){

        var filename = input.split('\\');
        
        return filename[ filename.length - 1 ];
    };
    /**
     * @returns {CodersController}
     */
    this.bind = function(){

        var _controller = this;

        document.addEventListener('DOMContentLoaded',function(e){

            _repo.view = new CodersView();
            
            _repo.server = new CodersModel();

        });
        
        //console.log( this.url( false ,true));
        
        return this;
    };
    
    return this.bind(/*setup client*/);
})( /* autosetup */ );


