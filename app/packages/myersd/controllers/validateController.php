<?php

class validateController extends \myersd\core\controller {

	public function indexAction() {
		echo '<pre>';
		echo 'validateController indexAction',chr(10);

		$foo = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

		$reply = $this->c->validate->single('trim|contains[b]',$foo,'Foo');

		var_dump($reply);
		var_dump($this->c->validate->error_array());
		var_dump($this->c->validate->errors_detailed());

	}

	public function multipleAction() {
		$v = [
			'name'=>['name'=>'name','label'=>'Name','rules'=>'ends_with[foo]'],
			'age'=>['name'=>'age','label'=>'Age','rules'=>''],
		];

		$data['name'] = 'Don Myers';
		$data['age'] = 21;
		$data['foo'] = 'bar';

		$reply = $this->c->validate->multiple($v,$data);

		echo '<pre>';
		var_dump($reply);
		var_dump($this->c->validate->error_array());
		var_dump($this->c->validate->errors_detailed());
		var_dump($data);
	}

} /* end validate */