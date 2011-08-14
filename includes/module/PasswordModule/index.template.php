<style type="text/css">
    .password-block {
        margin: 10px 0;
        width: 500px;
        clear:both;
    } 

    .double {
        margin-top:15px;
    }
    
    .password-block label {
        float: left;
        width: 180px;
    } 
    
    .password-block input {
        width: 300px;
    }
</style>

<div class="password-block"><label>Старый пароль:</label><input type="password" id="old-password" /></div>
<div class="password-block double"><label>Новый пароль:</label><input type="password" id="new-password" /></div>
<div class="password-block"><label>Повторите новый пароль:</label><input type="password" id="new-password-retype" /></div>
<div class="password-block double"><button id="change-password" disabled="disabled" class="styled-button"><b><b>Изменить пароль</b></b></button></div>

<script type="text/javascript">

$(document).ready( function()
{
    $( '.password-block input' ).keypress( function ( event ) {
        if( event.keyCode == 13 && ! $( '#change-password' ).attr( 'disabled' ) ) {
            $( this ).trigger( 'change' );
            $( '#change-password' ).trigger( 'click' );
        }
    } );
    
    // Показ кнопки сохранения
    $( '#old-password' ).bind( 'keyup change focus blur', function ( event ) {
        $( '#change-password' ).attr( 'disabled', ! /./.test( $(this).val() ) );
    } );
    
    // Смена пароля
    $( '#change-password' ).click( function ( e ) {
        // Новый пароль достаточно длинный?
        if( $( '#new-password' ).val().length < 4 ) {
            alert( $( '#new-password' ).val().length ? 'Слишком короткий новый пароль, нужно ввести не меньше четырех символов.' : 'Введите новый пароль.' );
            $( '#new-password' ).focus();
            $( '#new-password-retype' ).val( '' );
            return false;
        }
        
        // Новый пароль совпадает с его повтором?
        if( $( '#new-password' ).val() != $( '#new-password-retype' ).val() ) {
            alert( $( '#new-password-retype' ).val().length ? 'Вы неправильно набрали повтор нового пароля. Введите еще раз, пароль и его повтор должны совпадать.' : 'Введите повтор нового пароля.' );
            $( '#new-password-retype' ).val( '' ).focus();
            return false;
        }

        // Все проверено, можно отправить запрос
        $.post( "<?=$this->ajaxUri?>", { old_password: $( '#old-password' ).val(), new_password: $( '#new-password' ).val() },
        function( r )
        {
            if( r.success )
            {
                $( '#old-password, #new-password, #new-password-retype' ).val( '' ).trigger( 'change' ).blur();
                alert( 'Пароль изменен.' );
            }
            else
            {
                alert( r.message );
                $( '#old-password' ).focus();
            }
        }, 'json' );
    } );

    $( '#old-password' ).focus();
} );

</script>
