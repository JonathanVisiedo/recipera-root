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
                                    <label for="i_barcode"><i class="fa fa-barcode"></i>&nbsp;Code barre</label>
                                    <div class="group__display">
                                        <input type="text" name="i_barcode[]"  class="datacode" id="i_barcode" placeholder="Entrez votre code barre" required>
                                        <div class="display password__display"><i class="far fa-spin"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-l-4">
                                <div class="form__group">
                                    <label for="i_name">Nom de l'ingrédient</label>
                                    <input type="text" name="i_name[]" class="dataname" id="i_name" placeholder="Entrez le nom de votre ingrédient">
                                </div>
                            </div>
                            <div class="col-l-3">
                                <div class="form__group">
                                    <label for="i_quantity">Quantité de l'ingrédient (g)</label>
                                    <input type="text" name="i_quantity[]" id="i_quantity" placeholder="Entrez la quantité souhaitée">
                                </div>
                            </div>
                            <div class="col-l-1 ingredient--actions">
                                <button class="btn btn__secondary btn--rounded btn--small action--removeIngredient"><i class="fas fa-minus-circle"></i></button>
                            </div>
                        </div>`

        $('.ingredients').append(template)
    })

    $(document).on('click', '.action--removeIngredient', function(e) {
        e.preventDefault()
        $(this).parents('.row').remove()
    })

    /**
     * listen when a barcode is entered to GET data from API
     */

    $(document).on('change', '.datacode', function(e) {
        e.preventDefault()

        let input = $(this),
            display = $(this).siblings('.display'),
            dataname = $(this).parents('.row').find('.dataname'),
            barcode = $(this).val().trim('');

        input.removeClass('input__error')
        dataname.attr('readonly', 'readonly')
        display.removeClass('isHidden')

        $.ajax({
            url: `https://world.openfoodfacts.org/api/v0/product/${barcode}.json`,
            success: function(res) {
                if(res.status === 1) {

                    dataname.val(res.product.product_name)
                    display.addClass('isHidden')
                    input.addClass('input__success')
                    dataname.attr('readonly', false)

                    setTimeout(() => {
                        input.removeClass('input_success')
                    }, 1000)

                } else {
                    input.addClass('input__error')
                }
                console.log(res, res.product.product_name)
            }
        })

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

