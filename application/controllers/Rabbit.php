<?php

use Myth\Controllers\ThemedController;

/**
 * Rabbit Controller
 *
 * Auto-generated by Sprint on 2016-01-28 10:54am
 */
class Rabbit extends ThemedController {

    /**
     * The type of caching to use. The default values are
     * set globally in the environment's start file, but
     * these will override if they are set.
     */
    protected $cache_type      = null;
    protected $backup_cache    = null;

    // If set, this language file will automatically be loaded.
    protected $language_file   = null;

    // If set, this model file will automatically be loaded.
    protected $model_file      = 'rabbit_model';

    
    /**
     * Allows per-controller override of theme.
     * @var null
     */
    protected $theme = null;

    /**
     * Per-controller override of the current layout file.
     * @var null
     */
    protected $layout = null;

    /**
     * The UIKit to make available to the template views.
     * @var string
     */
    protected $uikit = '';

    /**
     * The number of rows to show when paginating results.
     * @var int
     */
    protected $limit = 25;


    //--------------------------------------------------------------------

    /**
     * The default method called. Typically displays an overview of this
     * controller's domain.
     *
     * @return mixed
     */
    public function index()
    {
        $this->load->library('table');

        $offset = $this->uri->segment( $this->uri->total_segments() );

        $rows = $this->rabbit_model->limit($this->limit, $offset)
                     ->as_array()
                     ->find_all();
        $this->setVar('rows', $rows);

		$this->render();
    }

    //--------------------------------------------------------------------

    /**
     * Create a single item.
     *
     * @return mixed
     */
    public function create()
    {
        $this->load->helper('form');
        $this->load->helper('inflector');

        if ($this->input->method() == 'post')
        {
            $post_data = $this->input->post();

            if ($this->rabbit_model->insert($post_data) )
            {
                $this->setMessage('Successfully created item.', 'success');
                redirect( site_url('rabbit') );
            }

            $this->setMessage('Error creating item. '. $this->rabbit_model->error(), 'danger');
        }

		$this->render();
    }

    //--------------------------------------------------------------------

    /**
     * Displays a single item.
     *
     * @param  int $id  The primary_key of the object.
     * @return mixed
     */
    public function show($id)
    {
        $item = $this->rabbit_model->find($id);

        if (! $item)
        {
            $this->setMessage('Unable to find that item.', 'warning');
            redirect( site_url('rabbit') );
        }

        $this->setVar('item', $item);

		$this->render();
    }

    //--------------------------------------------------------------------

    /**
     * Updates a single item.
     *
     * @param  int $id  The primary_key of the object.
     * @return mixed
     */
    public function update($id)
    {
        $this->load->helper('form');
        $this->load->helper('inflector');

        if ($this->input->method() == 'post')
        {
            $post_data = $this->input->post();

            if ($this->rabbit_model->update($id, $post_data))
            {
                $this->setMessage('Successfully updated item.', 'success');
                redirect( site_url('rabbit') );
            }

            $this->setMessage('Error updating item. '. $this->rabbit_model->error(), 'danger');
        }

        $item = $this->rabbit_model->find($id);
        $this->setVar('item', $item);

		$this->render();
    }

    //--------------------------------------------------------------------

    /**
     * Deletes a single item
     *
     * @param  int $id  The primary_key of the object.
     * @return mixed
     */
    public function delete($id)
    {
        if ($this->rabbit_model->delete($id))
        {
            $this->setMessage('Successfully deleted item.', 'success');
            redirect( site_url('rabbit') );
        }

        $this->setMessage('Error deleting item. '. $this->rabbit_model->error(), 'danger');
        redirect( site_url( 'rabbit' ) );
		
    }

    //--------------------------------------------------------------------
}