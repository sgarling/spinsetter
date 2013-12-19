<div class="song-card" >
    <div class="card" >
        <h2 onClick="loadProfile('{{IdUser}}','users');">{{Firstname}} {{Lastname}}</h2>
        <img src="<?=base_url()?>{{ProfilePicture}}"  onClick="loadProfile('{{IdUser}}','users');" />
        
    </div>
</div>