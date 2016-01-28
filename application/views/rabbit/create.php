<h2>Create A Rabbit</h2>

<?= form_open(); ?>

<div class='row'   >
<div class=' col-xs-12 col-sm-6 col-md-4'   >
<div class='form-group'   >
		<label for=''>Name</label>
            <input type='text' class='form-control' name='name' />
		</div>
<div class='form-group'   >
		<label for=''>Id</label>
            <input type='number' class='form-control' name='id' />
		</div>
<div class='form-group'   >
		<label for=''>Age</label>
            <input type='number' class='form-control' name='age' />
		</div>
</div>
</div>


    <input type="submit" name="submit" value="Create Rabbit" class="btn btn-primary" />
    &nbsp;or&nbsp;
    <a href="<?= site_url('rabbit') ?>">Cancel</a>

<?= form_close(); ?>
