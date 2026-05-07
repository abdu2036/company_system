<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $added_by_emp_id
 * @property int $company_id
 * @property string $asset_code
 * @property string|null $serial_number
 * @property string $name
 * @property string $category
 * @property string $location
 * @property string $status
 * @property string|null $notes
 * @property string|null $purchase_date
 * @property string $purchase_price
 * @property string|null $fault_description
 * @property string $maintenance_cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $received_by_emp_id
 * @property string|null $received_at
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaintenanceLog> $maintenanceLogs
 * @property-read int|null $maintenance_logs_count
 * @property-read \App\Models\Employee|null $receiver
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereAddedByEmpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereAssetCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereFaultDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereMaintenanceCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereReceivedByEmpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereUpdatedAt($value)
 */
	class Asset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $file_type
 * @property string $file_path
 * @property string $original_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereUpdatedAt($value)
 */
	class Attachment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string|null $chamber_number
 * @property string|null $issue_date
 * @property string|null $expiry_date
 * @property string|null $document_path
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereChamberNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chamber whereUpdatedBy($value)
 */
	class Chamber extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $cr_number
 * @property string $representative_name
 * @property string $phone
 * @property string $issue_date
 * @property string $expiry_date
 * @property string|null $document_path
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereCrNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereRepresentativeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommercialRegister whereUpdatedBy($value)
 */
	class CommercialRegister extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $activity
 * @property string $address
 * @property int $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \App\Models\Chamber|null $chamber
 * @property-read \App\Models\CommercialRegister|null $commercialRegister
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompanyDocument> $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\Importer|null $importer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\License|null $license
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedBy($value)
 */
	class Company extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $document_name
 * @property string $file_path
 * @property string $file_extension
 * @property string|null $file_size
 * @property string $document_type
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereFileExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyDocument whereUpdatedAt($value)
 */
	class CompanyDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $employee_code
 * @property int|null $branch_id
 * @property string|null $fingerprint_code
 * @property int $total_leave_balance
 * @property string $full_name
 * @property string $gender
 * @property string|null $date_of_birth
 * @property string|null $marital_status
 * @property string|null $qualification
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string $national_id
 * @property string|null $id_expiry_date
 * @property string|null $health_certificate_expiry
 * @property int $department_id
 * @property int $job_title_id
 * @property int $shift_id
 * @property int|null $manager_id
 * @property string $basic_salary
 * @property string|null $iban
 * @property string $hire_date
 * @property string|null $leaving_date
 * @property string $employment_type
 * @property string $status
 * @property string|null $profile_photo
 * @property string|null $id_proof
 * @property string|null $health_certificate_file
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $receivedAssets
 * @property-read int|null $received_assets_count
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereBasicSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmployeeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmploymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFingerprintCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereHealthCertificateExpiry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereHealthCertificateFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereHireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIdExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIdProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereJobTitleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLeavingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereQualification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereTotalLeaveBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUserId($value)
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FinanceService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinanceService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinanceService query()
 * @method static \Illuminate\Database\Eloquent\Builder|FinanceService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinanceService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinanceService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinanceService whereUpdatedAt($value)
 */
	class FinanceService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string|null $importer_number
 * @property string|null $issue_date
 * @property string|null $expiry_date
 * @property string|null $document_path
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder|Importer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Importer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Importer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereImporterNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereUpdatedBy($value)
 */
	class Importer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $total_amount
 * @property string $paid_amount
 * @property string $remaining_amount
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereRemainingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $service_name
 * @property string|null $action
 * @property int $quantity
 * @property string $price
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereServiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUpdatedAt($value)
 */
	class InvoiceItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string|null $license_number
 * @property string|null $tax_number
 * @property string|null $issue_date
 * @property string|null $expiry_date
 * @property string|null $document_path
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License query()
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereLicenseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedBy($value)
 */
	class License extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property int|null $technician_id
 * @property string $maintenance_type
 * @property string $cost
 * @property string|null $details
 * @property string $start_date
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @property-read \App\Models\Employee|null $technician
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereMaintenanceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereTechnicianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceLog whereUpdatedAt($value)
 */
	class MaintenanceLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee|null $employee
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

