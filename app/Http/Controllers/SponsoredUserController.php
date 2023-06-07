<?php

namespace App\Http\Controllers;

use App\Models\SponsoredUser;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class SponsoredUserController extends Controller
{
   public function addUser(Request $request){
         $sponsor_id = 41;
         $getChilds = $this->getChilds($sponsor_id);
         $totalChilds = count($getChilds);
            if($totalChilds == 0){
                $position = 'left';
            }elseif($totalChilds == 1){
                $position = 'middle';
            }
            elseif($totalChilds == 2){
                $position = 'right';
            }
            else{
                return response()->json(['message' => 'Sponsor has already 3 child'], 400);
            }

            $user = User::create([
                'name' => '5test',
                'email' => '5test@test.com',
                'password' => 'password',
            ]);

            $sponsoredUser = SponsoredUser::create([
                'user_id' => $user->id,
                'sponsor_id' => $sponsor_id,
                'parent_id' => $sponsor_id,
                'position' => $position,
            ]);

            return response()->json(['message' => 'User added successfully'], 200);
   }

   private function getChilds($sponsor_id){
         $childs = [];
         $childs = SponsoredUser::where('sponsor_id', $sponsor_id)->get();

         return $childs ?? [];
   }

   public function viewUsersNode(Request $request){
            $sponsor_id = 41;
            $childs = $this->getChilds($sponsor_id);
            $childs = $this->getChildsNode($childs);
            return response()->json(['childs' => $childs], 200);
   }

    private function getChildsNode($childs){
                $childsNode = [];
                foreach($childs as $child){
                 $childsNode[] = [
                      'id' => $child->user_id,
                      'name' => $child->user->name,
                      'position' => $child->position,
                        'childs' => $this->getChildsNode($this->getChilds($child->user_id)),
                 ];
                }
                return $childsNode;
    }

    public function viewAllUsersChild(){
        $users = User::all();
        $usersChild = [];
        foreach ($users as $user) {
            $usersChild[] = [
                'id' => $user->id,
                'name' => $user->name,
                'childs' => $this->getChildsNode($this->getChilds($user->id)),
            ];
        }

        return response()->json(['users' => $usersChild], 200);
    }
}
