<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserImportController extends Controller
{
    /**
     * Show the CSV import form.
     */
    public function showForm()
    {
        return view('admin.users.import');
    }

    /**
     * Process the CSV file upload.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'default_role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->withErrors(['csv_file' => 'Unable to read the CSV file.']);
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'The CSV file is empty.']);
        }

        // Normalize headers
        $header = array_map(fn($h) => strtolower(trim($h)), $header);
        $requiredHeaders = ['name', 'email'];

        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $header)) {
                fclose($handle);
                return back()->withErrors([
                    'csv_file' => "Missing required column: {$required}",
                ]);
            }
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $row = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            $record = array_combine($header, $data);

            // Validate each row
            $validator = Validator::make($record, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            ]);

            if ($validator->fails()) {
                $skipped++;
                $errors[] = "Row {$row}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            $role = !empty($record['role']) && Role::where('name', $record['role'])->exists()
                ? $record['role']
                : $request->input('default_role');

            $user = User::create([
                'name' => $record['name'],
                'email' => $record['email'],
                'password' => $record['password'] ?? 'changeme123',
            ]);

            $user->assignRole($role);
            $imported++;
        }

        fclose($handle);

        return redirect()->route('admin.users.index')
            ->with('success', "{$imported} users imported successfully.")
            ->with('import_errors', $errors)
            ->with('skipped', $skipped);
    }

    /**
     * Download the CSV import template.
     */
    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'role', 'password']);
            fputcsv($file, ['John Doe', 'john@example.com', 'student', 'password123']);
            fputcsv($file, ['Jane Smith', 'jane@example.com', 'instructor', 'password456']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
