<form>
<?php
/** @var TheNandan\Grids\DataProvider $data **/
/** @var TheNandan\Grids\Grid $grid **/
?>
<table class="table table-hover grid_table" id="<?php echo $grid->getConfig()->getName() ?>">

<?php echo $grid->header() ? $grid->header()->render() : '' ?>
<?php # ========== TABLE BODY ========== ?>
<tbody>
<?php while($row = $data->getRow()): ?>
    <?php echo $grid->getConfig()->getRowComponent()->setDataRow($row)->render() ?>
<?php endwhile; ?>
</tbody>
<?php echo $grid->footer() ? $grid->footer()->render() : '' ?>
</table>
<?php # Hidden input for submitting form by pressing enter if there are no other submits ?>
<input type="submit" style="display: none;" />
</form>
