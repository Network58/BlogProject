<!-- $users = Auth::user();
        if($users->userType == 'User'){
            return redirect('/login');
        }


        // $user = Auth::user(); -->
            
        protected $fillable = ['roleName', 'permissions'];


        Schema::defaultStringLength(191);

        $table->string('roleName');
            $table->text('permissions')->nullable();

            ->
        // $blog = Blog::with(['tag', 'cat'])->where('id', $reuest->id)->first();
        // if($blog){
        //     return $blog;
        // }else{
        //     return 'notdone';
        // }

            
            