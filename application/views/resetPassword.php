<script>
    
    $(document).ready(function(){
        users.Email='<?=$Email?>';
        users.Token='<?=$Token?>';
        users.getUser(users.Email);
        
        $('#sign-up').hide();
        $('#reset').show(); 
    });
    
    
</script>