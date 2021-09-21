/**
 * MASTER JS RECIPERA 2021
 */

const breakpoints = {

    s: 100,
    m: 200,
    l: 300,
    xl: 400

};



// on load
$(function() {

    flash()
    passwordDiscover()
    richEditor()

})
const flash = _ => {
    $('.alert .close').on('click', function() {
        $(this).parents('.alert').remove();
    })
}
const passwordDiscover = _ => {
    $('.password__display').on('click', function(e) {
        e.preventDefault()
        let input = $(this).prev('input')
        let type = input.attr('type')

        $(this).children('i').toggleClass('fa-eye').toggleClass('fa-eye-slash')

        if (type === 'password') {
            input.attr('type', 'text')
        } else if (type === 'text') {
            input.attr('type', 'password')
        }

    })
}
const richEditor = _ => {

    ClassicEditor
        .create( document.querySelector( '.ckeditor' ), {
            toolbar: {
                items: [
                    'bold',
                    'italic',
                    'link',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'undo',
                    'redo'
                ]
            },
            language: 'en',
            licenseKey: '',
        } )
        .then( editor => {
            window.editor = editor;
        } )
        .catch( error => {
            console.error( 'Oops, something went wrong!' );
            console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
            console.warn( 'Build id: asqtbxgym5wu-wvvi4djbxinq' );
            console.error( error );
        } );
}
const formCreateTools = _ => {

    $(document).on('click', '.action--addIngredient', function(e) {
        e.preventDefault()
        let template = `<div class="row">
                            <div class="col-l-4">
                                <div class="form__group">
                                    <label for="i_barcode"><i class="fa fa-barcode"></i>&nbsp;Barcode</label>
                                    <input type="text" name="i_barcode[]" id="i_barcode" placeholder="Enter your barcode">
                                </div>
                            </div>
                            <div class="col-l-4">
                                <div class="form__group">
                                    <label for="i_name">Name</label>
                                    <input type="text" name="i_name[]" id="i_name" placeholder="Enter your barcode">
                                </div>
                            </div>
                            <div class="col-l-3">
                                <div class="form__group">
                                    <label for="i_quantity">Quantity (mg)</label>
                                    <input type="text" name="i_quantity[]" id="i_quantity" placeholder="Enter your quantity">
                                </div>
                            </div>
                            <div class="col-l-1 ingredient--actions">
                                <button class="btn btn__primary btn--rounded btn--small action--removeIngredient"><i class="fas fa-minus-circle"></i></button>
                            </div>
                        </div>`

        $('.ingredients').append(template)
    })

    $(document).on('click', '.action--removeIngredient', function(e) {
        e.preventDefault()
        $(this).parents('.row').remove()
    })

}
const slugification = _ => {

    const slug  = (str) => {
        str = str.replace(/^\s+|\s+$/g, '') // trim
        str = str.toLowerCase()

        // remove accents, swap ñ for n, etc
        var from = "ÁÄÂÀÃÅČÇĆĎÉĚËÈÊẼĔȆĞÍÌÎÏİŇÑÓÖÒÔÕØŘŔŠŞŤÚŮÜÙÛÝŸŽáäâàãåčçćďéěëèêẽĕȇğíìîïıňñóöòôõøðřŕšşťúůüùûýÿžþÞĐđßÆa·/_,:"
        var to = "AAAAAACCCDEEEEEEEEGIIIIINNOOOOOORRSSTUUUUUYYZaaaaaacccdeeeeeeeegiiiiinnooooooorrsstuuuuuyyzbBDdBAa------"
        for (var i = 0, l = from.length;  i < l; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i))
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-') // collapse dashes

        return str
    }

    $('.toSlugify').each(function () {
        var $this = $(this)
        var source = $(this).data('source')
        $(document).on('change keyup', source, function () {
            $this.val(slug($(this).val()))
        })
        $(this).val(slug($($(this).data('source')).val()))
    })
}

