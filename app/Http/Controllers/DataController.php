namespace App\Http\Controllers;

use App\Models\Waste;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    public function index(Request $request)
    {
        $query = Waste::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('building', 'like', '%' . $request->search . '%')
                  ->orWhere('waste_type', 'like', '%' . $request->search . '%');
            });
        }

        // Date filter
        if ($request->date) {
            $query->where('date', $request->date);
        }

        // Pagination
        $perPage = $request->input('per_page', 20);

        $wastes = $query->orderBy('date', 'desc')->paginate($perPage);

        return view('waste.index', compact('wastes'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'building' => 'required|string',
            'waste_type' => 'required|string',
            'weight' => 'required|numeric',
        ]);

        Waste::create($request->all());

        return back()->with('success', 'Waste record added!');
    }


    public function update(Request $request, Waste $waste)
    {
        $request->validate([
            'date' => 'required|date',
            'building' => 'required|string',
            'waste_type' => 'required|string',
            'weight' => 'required|numeric',
        ]);

        $waste->update($request->all());

        return back()->with('success', 'Waste updated!');
    }


    public function destroy(Waste $waste)
    {
        $waste->delete();

        return back()->with('success', 'Waste deleted!');
    }
}
