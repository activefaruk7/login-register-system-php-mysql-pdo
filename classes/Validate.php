<?php


class Validate{
    private $_passed = false;
    /** @var DB */
    private $_db = null;
    private $_errors = array();

    public function __construct()
    {
        //$db = new DB();


        $this->_db = DB::getInstance();

        var_dump($this->_db);
    }


    public function check($request, $feilds = array()){

        foreach ($feilds as $feild => $rules) {

            foreach ($rules as $rule => $rule_val){
                $feildData = escape($request[$feild]);

                if($rule === 'required' && empty($feildData)){

                    $this->addError("{$feild} is required!");

                }elseif(!empty($feildData)){
                    switch ($rule){
                        case 'min':
                            if(strlen($feildData) < $rule_val){
                                $this->addError("{$feild} must be minimum of {$rule_val} chachters!");
                            }
                            break;
                        case 'max':
                            if(strlen($feildData) > $rule_val){
                                $this->addError("{$feild} must be maximum of {$rule_val} charchters!");
                            }
                            break;
                        case 'matches':
                            if($feildData != $request[$rule_val]){
                                $this->addError("{$feild} must be same!");
                            }
                            break;
                        case 'unique':

                            //var_dump($this->_db);

                            $check = $this->_db->get($rule_val, array($feild, "=", $feildData));

                            //$this->_db = DB::getInstance();



                            if($check->count()){
                                $this->addError("{$request[$feild]} already exist!");
                            }
                    }
                }
            }

        }

        if(empty($this->_errors)){

            return $this->_passed = true;

        }

    }

    public function addError($error){

        $this->_errors[] = $error;

    }

    public function errors() {
        return $this->_errors;
    }

    public function passed() {
        return $this->_passed;
    }



}

