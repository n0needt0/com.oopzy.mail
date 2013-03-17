<?php
require APPPATH.'/libraries/REST_Controller.php';
require APPPATH. '/libraries/Oopzy_lib.php';

/*this class wraps api to promis*/
Class api extends REST_Controller
{
    public function __construct()
    {
      parent::__construct();

      $jsonp = $this->get('callback');

      if(!empty($jsonp))
      {
          $this->response->format = 'jsonp';
      }
      else
      {
          $this->response->format = 'json';
      }
    }

    public function index()
    {
        die;
    }

    /*
     * returns list of messages in box
     * args box(box id), page(page number if more than one)]
     */
function messages_get($box='')
{

     $box = urldecode($box);

      if(empty($box))
      {
          die;
      }

      $redis = new Redis();

      try{

        $redis->connect(REDISHOST, REDISPORT);
        //first get keys for this box
        $keymap = $box . '@' . HOST_NAME . ':*';

        $keys = $redis->keys($keymap);

        //then all messages
        $res = $redis->mGet($keys);
        //process messages
        $result = array();

        if($res)
        {
            foreach($res as $key=>$data)
            {
                $data = unserialize($data);

                if(isset($data['email']))
                {
                    $email = $data['email'];

                    $dt = array();
                    include_once(APPPATH . '/libraries/vendors/PlancakeEmailParser/PlancakeEmailParser.php');
                    $emailParser = new PlancakeEmailParser($email);
                    $dt['to'] = $emailParser->getTo();
                    $dt['key'] = $keys[$key];
                    $dt['safe_key'] = md5($keys[$key]);
                    $dt['from'] = $emailParser->getHeader('from');
                    $dt['dt'] = $emailParser->getHeader('Date');
                    $dt['subject'] = $emailParser->getSubject();
                    $dt['debug'] = $data['debug'];

                    $emailDeliveredToHeader = $emailParser->getHeader('Delivered-To');

                    $dt['body'] = $emailParser->getPlainBody();

                    if(strlen($dt['body']) > 140)
                    {
                        $dt['body'] = substr($dt['body'],0,140);
                    }

                    $result[] = $dt;
                }
            }
         }
          $this->response($result,200);
          die;
      }
      catch(Exception $e)
      {
          $this->response(array('error' => $e->getMessage()));
          die;
      }
}


    /*
     * get message
     * args [message id]
     */
function message_get($key='')
{

     $key = urldecode($key);

      if(empty($key))
      {
          die;
      }

      $redis = new Redis();

      try{

        $redis->connect(REDISHOST, REDISPORT);
        //first get keys for this box

        $res = $redis->get($key);
        //process messages
        $result = array();

        if($res)
        {
                $data = unserialize($res);

                if(isset($data['email']))
                {
                    $email = $data['email'];

                    $dt = array();
                    include_once(APPPATH . '/libraries/vendors/PlancakeEmailParser/PlancakeEmailParser.php');
                    $emailParser = new PlancakeEmailParser($email);
                    $dt['to'] = $emailParser->getTo();
                    $dt['key'] = $key;
                    $dt['safe_key'] = md5($key);
                    $dt['from'] = $emailParser->getHeader('from');
                    $dt['dt'] = $emailParser->getHeader('Date');
                    $dt['subject'] = $emailParser->getSubject();
                    $dt['debug'] = $data['debug'];

                    $emailDeliveredToHeader = $emailParser->getHeader('Delivered-To');

                    $dt['body'] = $emailParser->getPlainBody();

                    $result[] = $dt;
                }
         }
          $this->response($result);
          die;
      }
      catch(Exception $e)
      {
          $this->response(array('error' => $e->getMessage()));
          die;
      }
}


    /*
     * verifies email ownership for a box
     * args [token char(32)]
     */
    public function verify_token_get($token)
    {
         $token = urldecode($token);

          $verify = new Oopzy_lib();

          if(!$verify->verify_token($token))
          {
              $this->response($verify->get_error());
              die;
          }

          $data = array('status'=>'verified', 'oopzybox'=>$verify->oopzybox,'to'=>$verify->to);

          $this->response($data);
    }

    /*
     * delete messages by boxname
     * args [box]
     */
    public function messages_delete(){}

    /*
     * delete message by id
     * args[id]
     */
    public function message_delete_get($key)
    {
      $key = urldecode($key);

      if(empty($key))
      {
        die;
      }

      $redis = new Redis();

      try{

        $redis->connect(REDISHOST, REDISPORT);

        $keys = $redis->delete($key);
        $this->response(array('error'=>''));
        die;
      }
      catch(Exception $e)
      {
        $this->response(array('error' => $e->getMessage()));
        die;
      }

    }

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

    public function save_message_post()
    {
        $ref = $_POST['ref'];
        $toemail = $_POST['to'];
        $auto = $_POST['auto'];
        $autofrom = $_POST['autofrom'];

        if(empty($ref) || empty($toemail))
        {
            $this->response(array('error'=>"nothing to process: POST empty"));
        }

         $tmp = explode(':',$ref);
         $tmp = explode('@',$tmp[0]);
         $box = $tmp[0];


         $redis = new Redis();

          try{

                  $redis->connect(REDISHOST, REDISPORT);

                  $oopzy = new Oopzy_lib();

                  $message = '';

                  //see if link is established already and we can process
                  $res = $oopzy->is_linked($box, $toemail);

                  if( !$res)
                  {
                        //send verification emai;

                        $v = $oopzy->verify_email($toemail, $box);

                        if(!$v)
                        {
                             $message =  $oopzy->get_message($oopzy->get_error());
                             $this->response(array('status'=>$message));
                        }

                         $message = $oopzy->get_message('MSG_SAVE_MESSAGE_VERIFY');
                  }

                  $oopzy->do_remail_queue($ref, $box, $toemail);

                  //set autoremail here
                  if(!empty($auto))
                  {
                      $oopzy->automate($autofrom);
                  }

              }
                catch(Exception $e)
              {
                  $message = $e->getMessage();
              }

         $this->response(array('error'=>$message));

    }
}
