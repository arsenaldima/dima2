class AdminCommand extends CConsoleCommand {
    public function run($args) {
      $newUser = new Users;
	  $newUser->username =$args[1];
	  $newUser->password = md5('lkjhgfd'.$args[2]);
	  $newUser->email=$args[3];
	  $newUser->created=tyme();
	  $newUser->role=3;
	  
	  if($newUser->save())
	  echo"Admin created!!";
	  else
	  echo "Admin not created!!";
    }
}