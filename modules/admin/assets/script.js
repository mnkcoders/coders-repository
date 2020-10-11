/**
 * 
 * @returns {CodersView}
 */
function CodersView(){
    
    var _elements = {
        'dropZone': null,
        'tabs':null,
        'collectionBox':null,
        'uploader':null
    };
    
    this.getContainer = function(){
        return document.getElementById( 'repository' );
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
     * Tab box
     * @returns {Element}
     */
    this.tabs = function(){
        
        _elements.tabs = this.element('ul');
        
        return _elements.tabs;
    };
    /**
     * Collection Box
     * @returns {Element}
     */
    this.collectionBox = function(){
        
        _elements.collectionBox = this.element('div');
        
        
        return _elements.collectionBox;
    };
    
    this.uploader = function(){
      
        var _view = this;
       
        var inputFile = this.element('input',{
            'type':'file',
            'name':'upload',
            'multiple':true,
            //'accept':this.acceptedTypes().join(', '),
            'id': _repo.inputs.dropzone + '_input'
        });
        inputFile.addEventListener( 'click', e => {
            console.log('Selecting files...');
            return true;
        });
        var inputSize = this.element('input',{
            'type':'hidden',
            'name':'MAX_FILE_SIZE',
            'value':_repo.options.fileSize});
        var inputLabel = this.element('label',
            {'for':inputFile.id,'class':'dropbox step current'},
            'Select or drop your files here');
        inputLabel.addEventListener( 'click', e => {
            return true;
        });
        var pBarContainer = this.element('div',
            {'class':'progress-bar step'},
            this.element('span',{'class':'progress-status'}));
        var btnUpload = this.element('button',{
            'type':'submit',
            'name':'action',
            'value':'upload',
            'class':'button button-large icon-upload'
        },'Upload')
        var lblCaption=  this.element('label',{'class':'caption'});
        //lblCaption.innerHTML = 'Ready to upload';
        var formData = this.element('form',{
            'method':'POST',
            'action':this.url({'action':'upload'}),
            'enctype':'multipart/form-data',
            'class':'form-container step'
        });
        formData.appendChild(btnUpload);
        formData.appendChild(inputSize);
        formData.appendChild(inputFile).addEventListener( 'change',function(e){
            e.preventDefault();
            btnUpload.innerHTML = _view.cleanFileName( this.value.toString( ) );
            console.log( typeof this.value );
            console.log( this.value );
            console.log( btnUpload.innerHTML );
            //inputLabel.classList.remove('current');
            //formData.classList.add('current');
            _view.setStep(formData, 'Ready to upload!' );
            return true;
        });
        var btnClose = this.element('span',{'class':'close'});
        btnClose.addEventListener('click',function(e){
            e.preventDefault();
            _view.closeUploader();
            /*document.querySelectorAll('.coders-repo.drop-zone').forEach(function(item){
                item.classList.remove('show');
            });*/
            return true;
        });
        var dropZone = this.element('div',{'class':'coders-repo drop-zone container'});
        dropZone.appendChild(btnClose);
        dropZone.appendChild(inputLabel);   //1st
        dropZone.appendChild(lblCaption);
        dropZone.appendChild(formData);     //2nd
        dropZone.appendChild(pBarContainer);//3rd
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
                        inputLabel.classList.remove('current');
                        //pBarContainer.classList.add('current');
                        var files = e.dataTransfer.files;
                        _view.resetQueue( files )
                                .attemptUpload( )
                                .setStep(pBarContainer, 'Uploading ...');
                        break;
                }
            }, false);
        });
        
        _elements.uploader = this.element('div',
            {'class':'container drop-zone','id':_repo.inputs.dropzone},
            dropZone);
        _elements.uploader.addEventListener( 'click', function(e){
            e.preventDefault();
            this.classList.remove('show');
            return true;
        });

        return _elements.uploader;
    };
    
    
    /**
     * @returns {CodersView}
     */
    this.initialize = function(){
        
        this.getContainer().appendChild( this.tabs( ) );
        this.getContainer().appendChild( this.collectionBox( ) );
        this.getContainer().appendChild( this.uploader( ) );
        return this;
    };
    
    return this.initialize();
}
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
        'collection':'default',
        //'URL': typeof URL !== 'undefined' ? URL : null,
        'timeout': 2000,
        'inputs':{
            'dropzone':'coders-repo-dropzone',
            'uploader':'coders-repo-uploader'
        },
        'options':{
            'fileSize': 256 * 256 * 256
        },
        'queue': {
            'files':[],
            'uploaded':0
        },
        'view': new CodersView(),
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
    this.getContainer = function(){
        return document.getElementById( 'repository' );
    };
    /**
     * @returns {HTMLDivElement}
     */
    this.getDropZone = function(){
        
        var dropZone = document.getElementById( _repo.inputs.dropzone );
        
        return dropZone !== null ? dropZone : this.appendUploader();
    };
    /**
     * @returns {Element}
     */
    this.getUploadButton = function(){
        
        return document.getElementsByClassName( _repo.inputs.uploader );
    };
    /**
     * @returns {CodersController}
     */
    this.server = function(){
        
        return this;
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
     * @returns {CodersController}
     */
    this.createCollection = function( collection ){
        
        return this;
    };
    /**
     * @param {String} collection
     * @returns {CodersController}
     */
    this.collectionOBSOLETE = function( collection ){
        
        return this.ajax( 'collection' , {'collection':collection} , function( resources ){
            
            //clear up the current view (shitty JS option)
            this.repoContainer().innerHTML = '';
            
            //foreach resource, append into the current list view
            
            console.log( resources );
        });
        
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
     * @returns {CodersController}
     */
    this.updateProgressBar = function( progress ){
        
        document.querySelectorAll('.coders-repo.drop-zone .progress-bar .progress-status').forEach( function( bar ){
            var value = parseInt( progress * 100 );
            bar.style.width = value.toString() + '%';
            bar.innerHTML = value + '%';
        });
        
        return this;
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
     * @returns {Element}
     */
    this.repoContainer = function(){
        return document.getElementById('coder-repo-collection');
    };
    /**
     * @returns {Element}
     */
    this.collectionsContainer = function(){
        return document.querySelectorAll('.nav-list.collections');
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
        //append content
        switch( true ){
            case (typeof content === 'object' && content instanceof Element):
                e.appendChild(content);
                break;
            case ( typeof content === 'string' ):
                e.innerHTML = content;
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
    
    this.displayCollection = function( name ){
        
        var box = this.element('div',{'class':'container ' + name ,'id':name } );
                
        var collection = this.element('ul',{'class':'collection'});
        
        box.appendChild( collection );
        
        return  box;
    };
    /**
     * @returns {Element}
     */
    this.collectionCreator = function(){
        
        var text = this.element('input',
            {'type':'text','name':'collection','placeholder':'Name your collection'});
        
        var button = this.element('button',
            {'type':'submit','name':'action','value':'create'},
            'Create');
            
        var box = this.element('div',{'class':'box create-collection'});
        
        box.appendChild( text );
        box.appendChild( button );
        
        return box;
    };
    /**
     * Collection Tab Header
     * @returns {CodersController}
     */
    this.createContent = function(){
        
        var tabs = this.element('ul',
            {'class':'collection-list','id':'collection-tab'},
            this.element('li',{'class':'item create-collection'},'New'));
        var container = this.element('div',
            {'class':'container','id':'collection-box'},
            this.collectionCreator());
        
        this.getContainer().appendChild( tabs );
        this.getContainer().appendChild( container );
        this.getContainer().appendChild( this.appendUploader());
        
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
     * @param {Element} element
     * @param {String} message 
     * @returns {CodersController}
     */
    this.setStep = function( element , message ){
        
        document.querySelectorAll('.drop-zone.container .step').forEach( function( item ){
            item.classList.remove('current');
        });
        if( element !== null ){
            element.classList.add('current');
        }
        
        if( typeof message !== 'undefined'){
            document.querySelectorAll('.drop-zone.container .caption').forEach( function( item ){
                item.innerHTML = message;
            });
        }
        
        return this;
    };
    /**
     * @returns {CodersController}
     */
    this.closeUploader = function(){
        document.querySelectorAll('.coders-repo.drop-zone').forEach(function(item){
            item.classList.remove('show');
        });
        return this;
    };
    /**
     * 
     * @returns {HTMLElement}
     */
    this.appendUploadButton = function(){
        
        var _controller = this;
        
        var item = document.createElement('li');
        item.className = 'item';
        var button = document.createElement('button');
        button.className = 'coders-repo-uploader content icon-upload large-icon';
        button.type = 'button';
        button.innerHTML = 'Upload';
 
        button.addEventListener( 'click' , function(e){

            e.preventDefault();
            
            if( _repo.debug ){
                console.log('Opening uploader!!');
            }
            
            _controller.getDropZone().classList.add('show');

            return true;
        });
        
        item.appendChild( button );
            
        return item;
    };
    /**
     * @returns {HTMLDivElement}
     */
    this.appendUploader = function( ){
        
        var _controller = this;
       
        var inputFile = this.element('input',{
            'type':'file',
            'name':'upload',
            'multiple':true,
            //'accept':this.acceptedTypes().join(', '),
            'id': _repo.inputs.dropzone + '_input'
        });
        inputFile.addEventListener( 'click', e => {
            console.log('Selecting files...');
            return true;
        });
        var inputSize = this.element('input',{
            'type':'hidden',
            'name':'MAX_FILE_SIZE',
            'value':_repo.options.fileSize});
        var inputLabel = document.createElement('label');
        inputLabel.setAttribute('for' , inputFile.id );
        inputLabel.className = 'dropbox step current';
        inputLabel.innerHTML = 'Select or drop your files here';
        inputLabel.addEventListener( 'click', e => {
            return true;
        });
        var pBarContainer = document.createElement('div');
        pBarContainer.className = 'progress-bar step';
        var pBarProgress = document.createElement('span');
        pBarProgress.className = 'progress-status';
        pBarContainer.appendChild(pBarProgress);
        var btnUpload = document.createElement('button');
        btnUpload.type = 'submit';
        btnUpload.name = 'task';
        btnUpload.value = 'upload';
        btnUpload.className = 'btn btn-big icon-upload';
        btnUpload.innerHTML = 'Upload';
        var lblCaption=  document.createElement('label');
        lblCaption.className = 'caption';
        //lblCaption.innerHTML = 'Ready to upload';
        var formData = document.createElement('form');
        formData.method = 'POST';
        formData.action = this.url({'task':'upload'});
        formData.enctype = 'multipart/form-data';
        formData.className = 'form-container step';
        formData.appendChild(btnUpload);
        formData.appendChild(inputSize);
        formData.appendChild(inputFile).addEventListener( 'change',function(e){
            e.preventDefault();
            btnUpload.innerHTML = _controller.cleanFileName( this.value.toString( ) );
            console.log( typeof this.value );
            console.log( this.value );
            console.log( btnUpload.innerHTML );
            //inputLabel.classList.remove('current');
            //formData.classList.add('current');
            _controller.setStep(formData, 'Ready to upload!' );
            return true;
        });
        var btnClose = document.createElement('span');
        btnClose.className = 'close';
        btnClose.addEventListener('click',function(e){
            e.preventDefault();
            _controller.closeUploader();
            /*document.querySelectorAll('.coders-repo.drop-zone').forEach(function(item){
                item.classList.remove('show');
            });*/
            return true;
        });
        var dropZone = document.createElement('div');
        dropZone.className = 'coders-repo drop-zone container';
        dropZone.appendChild(btnClose);
        dropZone.appendChild(inputLabel);   //1st
        dropZone.appendChild(lblCaption);
        dropZone.appendChild(formData);     //2nd
        dropZone.appendChild(pBarContainer);//3rd
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
                        inputLabel.classList.remove('current');
                        //pBarContainer.classList.add('current');
                        var files = e.dataTransfer.files;
                        _controller.resetQueue( files )
                                .attemptUpload( )
                                .setStep(pBarContainer, 'Uploading ...');
                        break;
                }
            }, false);
        });
        
        var dropModal = this.element('div',
            {'class':'container drop-zone','id':_repo.inputs.dropzone},
            dropZone);
        dropModal.addEventListener( 'click', function(e){
            e.preventDefault();
            this.classList.remove('show');
            return true;
        });
        return dropModal;
        //return document.body.appendChild( dropModal );
    };
    
    /**
     * @returns {CodersController}
     */
    this.bind = function(){

        var _controller = this;

        document.addEventListener('DOMContentLoaded',function(e){

            _controller.createContent();

            /*var uploadButton = _controller.getUploadButton();

            if( uploadButton !== null ){

                Array.prototype.forEach.call( uploadButton , function( btn ){

                    btn.addEventListener( 'click' , function(e){
                        e.preventDefault();
                        console.log('Opening uploader!!');
                        _controller.getDropZone().classList.add('show');
                        return true;
                    });
                });
            }*/
            
            //_controller.collections().list(/*load content list*/);
        });
        
        //console.log( this.url( false ,true));
        
        return this;
    };
    
    return this.bind(/*setup client*/);
})( /* autosetup */ );


