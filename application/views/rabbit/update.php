<h2>Edit Rabbit</h2>

<?= form_open(); ?>

<div class='row'   >
<div class=' col-xs-12 col-sm-6 col-md-4'   >
<div class='form-group'   >
		<label for=''>Name</label>
            <input type='text' name='name' class='form-control' value='<?= set_value('name', $item->name ) ?>' />
		</div>
<div class='form-group'   >
		<label for=''>Id</label>
            <input type='number' name='id' class='form-control' value='<?= set_value('id', $item->id ) ?>' />
		</div>
<div class='form-group'   >
		<label for=''>Age</label>
            <input type='number' name='age' class='form-control' value='<?= set_value('age', $item->age ) ?>' />
		</div>
</div>
</div>

<input type="submit" name="submit" class="btn btn-primary" value="Save Rabbit" />
&nbsp;or&nbsp;
<a href="<?= site_url('rabbit') ?>">Cancel</a>

<?= form_close(); ?>
