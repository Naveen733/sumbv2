<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignupMail;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

use App\Models\SumbUsers;
use App\Models\SumbClients;


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
                'file' => 'required|mimes:csv,txt,xlx,xls,xlsx,pdf|max:2048'
            ]);
            if(!empty($request->file))
            {
                $docpath = $request->file('file')->store('avatars');
                $docModel = new Document;
                $originalname = $request->file->getClientOriginalName();
                $extensionname = $request->file->extension();
                $docModel->name = $originalname;
                $docModel->originalname = $originalname;
                $docModel->extensionname = $extensionname;
                $docModel->encryptname = $docpath;      
                $docModel->save();
        
                return redirect()->back()
                        ->with('success','Document has been uploaded.')
                        ->with('file', $docpath); 
            }
    }


    public function downloadfile(Request $request)
    {
        $document = Document::where('id', $request->id)->first();
        $name = $document->name;
        $newname = $name;   
        return Storage::download($document->encryptname, $newname);
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
        return redirect()->route('doc-upload')->with('success', 'Document has been updated');        
    }

    public function destroy(Request $request)
    {          
        $docid = $request->id;
        if (isset($docid)) {             
            $document = Document::findOrFail($docid);
            $document->delete();
        } 
        return redirect()->route('doc-upload')->with('success', 'Document has been deleted');   
    }
}