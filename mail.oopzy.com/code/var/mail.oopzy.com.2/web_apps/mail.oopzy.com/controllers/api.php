<?php
require APPPATH.'/libraries/REST_Controller.php';

/*this class wraps api to promis*/
Class oopzy_API extends REST_Controller
{
    public function __construct()
    {
         parent::__construct();
         $this->user = 'EE3F725D-0E40-4A8C-B096-BF5013C6FEEB';
         $this->token = '5A1E8030-1040-4DA3-91DA-F766C0037801';
         $this->output_format = 'json';
         $this->protocol = 'https';
    }

    /*
     * gets redis server info for a hash
     * args [hash]
     */
    public function server_info_get(){}

    /*
     * returns list of messages in box
     * args box(box id), page(page number if more than one)]
     */
    public function messages_get(){}

    /*
     * get message
     * args [message id]
     */
    public function message_get(){}

    /*
     * verifies email ownership for a box
     * args [token char(32)]
     */
    public function token_verify_get(){}

    /*
     * delete messages by boxname
     * args [box]
     */
    public function messages_delete(){}

    /*
     * delete message by id
     * args[id]
     */
    public function message_delete(){}

    /*
     * Get new random mail box
     */
    public function new_box_get(){}

    /*
     * get new random box and optionally link it to email for a string rule
     * args [forwardto string , allowfrom array of strings ]
     */
    public function new_box_put(){}

    /*
     * create link between box and email
     * args box, email
     */
    public function link_put(){}

    /*
     * delete link between box and email
     * args box, mailto
     */
    public function delete_link_put(){}
}
