/**
 * https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
 * 
 * @param {String} URL
 * @returns {CodersRepository}
 */
function CodersRepository( URL ){
    /**
     * @type CodersRepository
     */
    var _controller = this;
    
    var _repo = {
        'collection':'default',
        'URL': typeof URL !== 'undefined' ? URL : null,
        'timeout': 2000
    };
    /**
     * @returns {String}
     */
    this.url = function(){
        return _repo.URL + '?page=coders-repository';
    };
    /**
     * @returns {HTMLDivElement}
     */
    this.getDropZone = function(){
        return document.getElementById('coders-repo-dropzone');
    };
    /**
     * @returns {CodersRepository}
     */
    this.server = function(){
        
        return this;
    };
    /**
     * @returns {CodersRepository}
     */
    this.notify = function( message ){
        
        return this;
    };
    /**
     * @returns {CodersRepository}
     */
    this.createCollection = function( collection ){
        
        return this;
    };
    /**
     * @param {Array | FileList} files 
     * @returns {CodersRepository}
     */
    this.uploadFiles = function( files ){
        
        if( files && files.length ){
            console.log( files );
            Array.prototype.forEach.call( files , function( upload ){

                //var formData = new FormData();
                /*formData
                        .append('coders.repo.upload[]', upload )
                        .append('coders.repo.action','upload');*/
                
                var formData = {
                    'coders.repo.upload[]': upload,
                    'coders.repo.action':'upload'
                };

                fetch( _controller.url(), { method: 'POST', body: formData} )
                    .then(function(response){
                        _controller.getDropZone().classList.add('completed');
                        console.log(response);
                        window.setTimeout(function(){
                            _controller.getDropZone().classList.remove('completed');
                        }, _repo.timeout );
                    }).catch( function( error ){
                        _controller.getDropZone().classList.add('error');
                        console.log(error);
                        window.setTimeout(function(){
                            _controller.getDropZone().classList.remove('error');
                        }, _repo.timeout );
                    });
            });
        }
        
        return this;
    };
    /**
     * @param {String} collection
     * @returns {CodersRepository}
     */
    this.loadCollection = function( collection ){
        
        return this;
    };
    /**
     * @returns {CodersRepository}
     */
    this.bind = function(){

        document.addEventListener('DOMContentLoaded',function(e){
            //initialize dropZone
            //var dropZone = this.getElementById('coders-repo-dropzone');
            var dropZone = _controller.getDropZone();
            //console.log(dropZone.toString());
            
            if( dropZone !== null ){
                ['dragenter','dragleave','dragover','drop'].forEach( function( event ){
                    dropZone.addEventListener(event, function(e){
                        console.log( event );
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
                                _controller.uploadFiles( e.dataTransfer.files );
                                break;
                        }
                    }, false);
                });
            }
            
            //load collections

        });

        return this;
    };
    
    return this.bind();
}
/**
 * @param {String | URL } URL
 * @returns {CodersRepository}
 */
CodersRepository.client = function( URL ){ return new CodersRepository( URL ); };