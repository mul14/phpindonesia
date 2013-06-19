<?php

/*
 * This file is part of the PHP Indonesia package.
 *
 * (c) PHP Indonesia 2013
 */

use app\Model\ModelBase;
use app\Model\ModelAuth;

class ModelAuthTest extends PhpindonesiaTestCase {
	protected $needDatabase = true;

	/**
	 * Cek konsistensi model Auth instance
	 */
	public function testCekKonsistensiModelAuth() {
		$auth = ModelBase::factory('Auth');

		$this->assertInstanceOf('\app\Model\ModelBase', $auth);
		$this->assertInstanceOf('\app\Model\ModelAuth', $auth);

		$this->assertEquals(55, ModelAuth::HASH_LENGTH);
		$this->assertEquals(15, ModelAuth::HASH_COUNT);
		$this->assertEquals(7, ModelAuth::MIN_HASH_COUNT);
		$this->assertEquals(30, ModelAuth::MAX_HASH_COUNT);
		$this->assertEquals('./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', ModelAuth::ALNUM);
	}

	/**
	 * Cek Register
	 */
	public function testCekRegisterModelAuth() {
		// Invalid data
		$auth = new ModelAuth();
		$data = array();
		$hasilRegister = $auth->register($data);

		$this->assertFalse($hasilRegister->get('success'));
		$this->assertNotEmpty($auth->getInspector()->getErrors());

		// Password tidak cocok
		$auth = new ModelAuth();
		$data = array('username' => 'foo', 'email' => 'foo@bar.com','password' => 'aman', 'cpassword' => 'tidak aman');
		$hasilRegister = $auth->register($data);

		$this->assertFalse($hasilRegister->get('success'));
		$this->assertArrayHasKey('password', $auth->getInspector()->getErrors());

		// Test exists username/email
		$auth = new ModelAuth();
		$this->createDummyUser();

		$data = array('username' => 'dummy', 'email' => 'valid@oot.com', 'password' => 'secret', 'cpassword' => 'secret');
		$hasilRegister = $auth->register($data);

		$this->assertFalse($hasilRegister->get('success'));
		$this->assertArrayHasKey('username', $auth->getInspector()->getErrors());

		$auth = new ModelAuth();
		$data = array('username' => 'valid', 'email' => 'dummy@oot.com', 'password' => 'secret', 'cpassword' => 'secret');
		$hasilRegister = $auth->register($data);

		$this->assertFalse($hasilRegister->get('success'));
		$this->assertArrayHasKey('email', $auth->getInspector()->getErrors());

		// Test valid proses
		$this->deleteDummyUser();

		$data = array('username' => 'dummy', 'email' => 'frei.denken@facebook.com', 'password' => 'secret', 'cpassword' => 'secret');

		$auth = new ModelAuth();
		$hasilRegister = $auth->register($data);

		$this->assertTrue($hasilRegister->get('success'));

		// Cek konfirmasi
		$this->assertFalse($auth->isConfirmed($hasilRegister->get('data')));
	}

	/**
	 * Cek reset password
	 */
	public function testCekKirimResetModelAuth() {
		$auth = new ModelAuth();

		// Invalid user
		$this->assertFalse($auth->sendReset('undefined'));

		$auth->createUser('dummy', 'frei.denken@facebook.com', 'secret');

		// Valid user
		$this->assertTrue($auth->sendReset('frei.denken@facebook.com'));
	}

	/**
	 * Cek Login
	 */
	public function testCekLoginModelAuth() {
		$auth = new ModelAuth();

		// Invalid data
		$data = array();
		$hasilLogin = $auth->login($data);

		$this->assertFalse($hasilLogin->get('success'));

		// Belum terdaftar
		$data = array('username' => 'undefined', 'password' => 'tidakvalid');
		$hasilLogin = $auth->login($data);

		$this->assertFalse($hasilLogin->get('success'));

		// Username/email valid, tapi password tidak cocok
		$this->createDummyUser();
		$auth = new ModelAuth();
		$data = array('username' => 'dummy', 'password' => 'oot');
		$hasilLogin = $auth->login($data);

		$this->assertFalse($hasilLogin->get('success'));

		// Valid user
		$auth = new ModelAuth();
		$data = array('username' => 'dummy', 'password' => 'secret');
		$hasilLogin = $auth->login($data);

		$this->assertTrue($hasilLogin->get('success'));
		$this->assertInstanceOf('\app\Parameter', $auth->getUser($hasilLogin->get('data')));
	}

	/**
	 * Cek Login via FB
	 */
	public function testCekLoginFacebookModelAuth() {
		$auth = new ModelAuth();
		$token = 'SomeToken';

		// Invalid data
		$data = array();
		$hasilLoginFacebook = $auth->loginFacebook($data,$token);

		$this->assertFalse($hasilLoginFacebook->get('success'));

		// Valid user
		$this->createDummyUser();
		$auth = new ModelAuth();

		$data = array('username' => 'dummy', 'email' => 'dummy@oot.com', 'id' => 123);
		$hasilLoginFacebook = $auth->loginFacebook($data, $token);

		$this->assertTrue($hasilLoginFacebook->get('success'));
	}

	/**
	 * Cek konfirmasi
	 */
	public function testCekConfirmModelAuth() {
		$auth = ModelBase::factory('Auth');

		$this->createDummyUser();
		$dummyUser = ModelBase::ormFactory('PhpidUsersQuery')->findOneByName('dummy');
		$dummyUserUid = $dummyUser->getUid();
		$dummyUserToken = $dummyUser->getPass();

		// Cek sebelum konfirmasi
		$this->assertFalse($auth->isConfirmed($dummyUserUid));

		// Do confirm
		$auth->confirm($dummyUserToken);

		// Cek setelah konfirmasi
		$this->assertTrue($auth->isConfirmed($dummyUserUid));
	}
}