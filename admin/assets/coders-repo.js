/**
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
        'URL': typeof URL !== 'undefined' ? URL : null
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
     * @returns {CodersRepository}
     */
    this.uploadFiles = function( files ){
        
        
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

        jQuery( document ).ready( function(){
            jQuery('.coders-repository .selector').each(function(){
                jQuery(this).on('click',function(e){
                    _controller.loadCollection( ( _repo.collection = jQuery(this).val() ) );
                    //console.log('Switched to ' + _repo.collection);
                    return true;
                });
            });
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