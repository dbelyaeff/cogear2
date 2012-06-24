<div class="taxonomy">
    <?php echo $vocabulary->name?>:
    <?php foreach($terms as $term):?>
        <?php echo $term->name?> 
    <?php endforeach;?>
</div>