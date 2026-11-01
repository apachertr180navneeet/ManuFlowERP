<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemAssignment;
use App\Models\Item;
use App\Models\Company;
use App\Models\Unit;
use Exception;

class ItemAssignmentController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index()
    {
        $user = Auth::user();
        $companies = [];
        $currentCompanyId = null;

        if ($user->role === 'admin') {
            $companies = Company::where('status', 'active')->orderBy('company_name')->get();
            $currentCompanyId = $companies->first()?->id;
        } else {
            $currentCompanyId = $user->company_id;
        }

        $finishedItems = Item::with('unit')->where('item_type', 'Finished')
            ->where('company_id', $currentCompanyId)
            ->where('status', 'active')
            ->get()->map(function ($item) {
                $item->unit_name = $item->unit?->unit_name ?? '';
                return $item;
            });

        $rawMaterials = Item::with('unit')->where('item_type', 'Raw Material')
            ->where('company_id', $currentCompanyId)
            ->where('status', 'active')
            ->get()->map(function ($item) {
                $item->unit_name = $item->unit?->unit_name ?? '';
                return $item;
            });

        $units = Unit::where('company_id', $currentCompanyId)
            ->where('status', 'active')
            ->orderBy('unit_name')
            ->get(['id', 'unit_name']);

        $assignmentRecords = ItemAssignment::where('company_id', $currentCompanyId)->get();

        $assignments = [];
        foreach ($assignmentRecords as $a) {
            if (!isset($assignments[$a->finished_item_id])) {
                $assignments[$a->finished_item_id] = [];
            }
            $assignments[$a->finished_item_id][$a->raw_material_id] = [
                'value' => $a->quantity,
                'unit_name' => $a->unit_name,
            ];
        }

        $companyName = $currentCompanyId ? Company::find($currentCompanyId)?->company_name : '';

        $matrixData = [
            'finished' => $finishedItems,
            'raw' => $rawMaterials,
            'assignments' => empty($assignments) ? new \stdClass() : $assignments,
            'units' => $units,
        ];

        return view('admin.item_assignment.index', compact(
            'companies', 'currentCompanyId', 'finishedItems', 'rawMaterials', 'assignments', 'companyName', 'matrixData', 'units'
        ));
    }

    public function getMatrixData($companyId = null)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            $companyId = $user->company_id;
        }

        if (!$companyId) {
            $first = Company::where('status', 'active')->first();
            $companyId = $first ? $first->id : null;
        }

        $finishedItems = Item::with('unit')->where('item_type', 'Finished')
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->get(['id', 'short_code', 'item_name', 'unit_id'])
            ->map(function ($item) {
                $item->unit_name = $item->unit?->unit_name ?? '';
                return $item;
            });

        $rawMaterials = Item::with('unit')->where('item_type', 'Raw Material')
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->get(['id', 'short_code', 'item_name', 'unit_id'])
            ->map(function ($item) {
                $item->unit_name = $item->unit?->unit_name ?? '';
                return $item;
            });

        $units = Unit::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('unit_name')
            ->get(['id', 'unit_name']);

        $assignmentRecords = ItemAssignment::where('company_id', $companyId)->get();

        $assignments = [];
        foreach ($assignmentRecords as $a) {
            if (!isset($assignments[$a->finished_item_id])) {
                $assignments[$a->finished_item_id] = [];
            }
            $assignments[$a->finished_item_id][$a->raw_material_id] = [
                'value' => $a->quantity,
                'unit_name' => $a->unit_name,
            ];
        }

        return response()->json([
            'finished' => $finishedItems,
            'raw' => $rawMaterials,
            'assignments' => (object)$assignments,
            'units' => $units,
        ]);
    }

    public function saveAll(Request $request)
    {
        try {
            $data = $request->json()->all();
            $user = Auth::user();

            if ($user->role === 'admin') {
                $companyId = $data['company_id'] ?? null;
            } else {
                $companyId = $user->company_id;
            }

            if (!$companyId) {
                return response()->json(['success' => false, 'message' => 'No company selected.'], 400);
            }

            $existing = ItemAssignment::where('company_id', $companyId)->get()->keyBy(function ($item) {
                return $item->finished_item_id . '-' . $item->raw_material_id;
            });

            $submittedKeys = [];
            $assignmentsData = $data['assignments'] ?? [];

            foreach ($assignmentsData as $finishedId => $raws) {
                foreach ($raws as $rawId => $item) {
                    $qty = $item['value'] ?? null;
                    if ($qty === '' || $qty === null || !is_numeric($qty)) continue;

                    $key = $finishedId . '-' . $rawId;
                    $submittedKeys[$key] = true;

                    $unitName = isset($item['unit_name']) && $item['unit_name'] !== '' ? $item['unit_name'] : null;

            if (isset($existing[$key])) {
                $existing[$key]->update([
                    'quantity' => $qty,
                    'unit_name' => $unitName,
                ]);
            } else {
                ItemAssignment::create([
                    'company_id' => $companyId,
                    'finished_item_id' => $finishedId,
                    'raw_material_id' => $rawId,
                    'quantity' => $qty,
                    'unit_name' => $unitName,
                    'status' => 'active',
                    'created_by' => Auth::id(),
                ]);
            }
                }
            }

            foreach ($existing as $key => $record) {
                if (!isset($submittedKeys[$key])) {
                    $record->delete();
                }
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred'], 500);
        }
    }

    public function toggleStatus(ItemAssignment $itemAssignment)
    {
        $this->authorizeAccess($itemAssignment);
        try {
            $itemAssignment->status = $itemAssignment->status === 'active' ? 'inactive' : 'active';
            $itemAssignment->save();
            return response()->json(['success' => true, 'status' => $itemAssignment->status]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred']);
        }
    }

    public function destroy(ItemAssignment $itemAssignment)
    {
        $this->authorizeAccess($itemAssignment);
        try {
            $itemAssignment->delete();
            return redirect()->route('admin.item-assignment.index')->with('success', 'Item assignment deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    protected function authorizeAccess(ItemAssignment $itemAssignment)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $itemAssignment->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
