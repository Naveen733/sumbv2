<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignupMail;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

use App\Models\SumbUsers;
use App\Models\SumbClients;
use App\Models\File;

class DocumentUploadController extends Controller
{
    public function __construct() {
        //none at this time
    }

    public function index(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Docs'
        );
        $doclist = Document::all();
        return view('docupload.doc-upload', $pagedata)->with(compact('doclist')); 
    }
 
    public function store(Request $request)
    {
         
        $request->validate([
            'doc' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048'
            ]);
            $docModel = new Document;
            if($request->doc()) {
                $docName = time().'_'.$request->file->getClientOriginalName();
                $docPath = $request->file('doc')->storeAs('Docs', $docName, 'public');
                $docModel->name = time().'_'.$request->file->getClientOriginalName();
                $docModel->path = '/storage/' . $docPath;
                $docModel->save();
                return redirect()->back()
                ->with('success','Document has been uploaded.')
                ->with('file', $docName);
            }
    }

    public function show($docid)
    {

    }

    public function docedit(Request $request)
    {
        $userinfo =$request->get('userinfo');
        $docid =$request->get('id');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'docid'=>$docid,
            'pagetitle' => 'Docs'
        );
        $doclist = Document::findOrFail($docid);
        $pagedata['data']=$doclist;
        return view('docupload.doc-edit', $pagedata);
        
    }

    public function doceditprocess(Request $request, $docid)
    {
        $updateData = $request->validate([
            'name' => 'required|max:255',
        ]);
        $newdoc = Document::whereId($docid)->update($updateData);
        
        if ($request->hasFile('newdoc'))
        {
            Storage::move(public_path('docs'), $request);
        }

        
        return redirect('/doc-upload')->with('completed', 'Document name has been updated');
    }

    public function destroy($id)
    {
        
    }

    public function moveImage(Request $request)
    {
        Document::move(public_path('exist/test.png'), public_path('move/test_move.png'));
   
        dd('dont copy file.');
    }
}
