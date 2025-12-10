<?php
namespace App\Http\Controllers;

use App\Models\WasteEntry;
use Illuminate\Http\Request;

class DataController extends Controller{
    public function destroy($id)
    {
        $entry = WasteEntry::findOrFail($id);
        $entry->delete();

        return redirect()->route('dashboard')->with('success', 'Entry deleted successfully.');
    }
}