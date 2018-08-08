<?php

namespace App\Http\Controllers;

use App\Role;
use App\Right;
use Illuminate\Http\Request;

class RightController extends Controller
{
    private $permissions;

    public function __construct() {
      $this->permissions = [
        'roles' =>
          (object) [
            'name' => 'Rôles',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        // sellsy datas, readonly
        'clients' =>
          (object) [
            'name' => 'Clients',
            'show' => true,
            'add' => false,
            'edit' => false,
            'delete' => false,
            'checked' => []
          ],
        'clients_details' =>
          (object) [
            'name' => 'Détails clients',
            'show' => true,
            'add' => false,
            'edit' => false,
            'delete' => false,
            'checked' => []
          ],
        'orders' =>
          (object) [
            'name' => 'Commandes',
            'show' => true,
            'add' => false,
            'edit' => false,
            'delete' => false,
            'checked' => []
          ],
        'invoices' =>
          (object) [
            'name' => 'Factures',
            'show' => true,
            'add' => false,
            'edit' => false,
            'delete' => false,
            'checked' => []
          ],
        'projects' =>
          (object) [
            'name' => 'Projets',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'contacts' =>
          (object) [
            'name' => 'Contacts',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'export_contacts' =>
          (object) [
            'name' => 'Exporter des contacts',
            'show' => true,
            'add' => false,
            'edit' => false,
            'delete' => false,
            'checked' => []
          ],
        'identifiers' =>
          (object) [
            'name' => "Types d'identifiants",
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'project_identifiers' =>
          (object) [
            'name' => 'Identifiants de projets',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'project_confidential_identifiers' =>
          (object) [
            'name' => 'Identifiants confidentiels',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'tags' =>
          (object) [
            'name' => 'Tags',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'users' =>
          (object) [
            'name' => 'Utilisateurs',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'project_urls' =>
          (object) [
            'name' => 'Urls de projet',
            'show' => true,
            'add' => false, // use 'projects' value
            'edit' => false, // use 'projects' value
            'delete' => false, // use 'projects' value
            'checked' => []
          ],
        // contact types
        'types' =>
          (object) [
            'name' => 'Types de contact',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'leave' =>
          (object) [
            'name' => 'Congés',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'expenses' =>
          (object) [
            'name' => 'Notes de frais',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'request_management' =>
          (object) [
            'name' => 'Gestion des demandes',
            'show' => false,
            'add' => false,
            'edit' => true,
            'delete' => false,
            'checked' => []
          ],
        'contracts' =>
          (object) [
            'name' => 'Contrats',
            'show' => true,
            'add' => true,
            'edit' => true,
            'delete' => true,
            'checked' => []
          ],
        'overtime' =>
          (object) [
            'name' => 'Heures supplémentaires',
            'show' => false,
            'add' => true,
            'edit' => false,
            'delete' => true,
            'checked' => []
          ],
        'pdf' =>
          (object) [
            'name' => 'PDF',
            'show' => true,
            'add' => false,
            'edit' => true,
            'delete' => false,
            'checked' => []
          ],
        'documents' =>
          (object) [
            'name' => 'Documents (fiche de paie, ...)',
            'show' => true,
            'add' => true,
            'edit' => false,
            'delete' => true,
            'checked' => []
          ]
      ];
    }

    public function permissions() {
      return response()->json([
        'success' => true,
        'data' => $this->permissions,
      ]);
    }

    public function rolePermissions(Role $role) {
      $rights = Right::where('role_id', $role->id)->get();
      if (empty($rights)) $rights = [];
      $permissions = $this->permissions;

      foreach ($rights as $right) {
        if (!isset($permissions[$right->name])) continue;

        // show
        if ($permissions[$right->name]->show && $right->show == 1) {
          $permissions[$right->name]->checked[] = 'show';
        }

        // add
        if ($permissions[$right->name]->add && $right->add == 1) {
          $permissions[$right->name]->checked[] = 'add';
        }

        // edit
        if ($permissions[$right->name]->edit && $right->edit == 1) {
          $permissions[$right->name]->checked[] = 'edit';
        }

        // delete
        if ($permissions[$right->name]->delete && $right->delete == 1) {
          $permissions[$right->name]->checked[] = 'delete';
        }
      }

      return response()->json([
        'success' => true,
        'data' => $permissions,
      ]);
    }
}
