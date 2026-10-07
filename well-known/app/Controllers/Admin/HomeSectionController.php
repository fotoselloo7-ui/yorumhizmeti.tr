<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Upload;

class HomeSectionController extends Controller
{
    public function index(): void
    {
        $sections = $this->db->fetchAll("SELECT * FROM home_sections ORDER BY sort_order ASC");
        $this->renderAdmin('admin/home-sections/index', [
            'pageTitle' => 'Ana Sayfa Yönetimi',
            'sections' => $sections,
        ]);
    }

    public function edit(string $id): void
    {
        $section = $this->db->fetch("SELECT * FROM home_sections WHERE id = ?", [(int) $id]);
        if (!$section) {
            flash('error', 'Bölüm bulunamadı.');
            redirect('/admin/ana-sayfa');
        }
        $icons = \App\Services\IconService::list();
        $this->renderAdmin('admin/home-sections/form', [
            'pageTitle' => 'Bölüm Düzenle: ' . $section['title'],
            'section' => $section,
            'icons' => $icons,
        ]);
    }

    public function update(string $id): void
    {
        Csrf::check();
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'subtitle' => trim($_POST['subtitle'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'button_text' => trim($_POST['button_text'] ?? ''),
            'button_url' => trim($_POST['button_url'] ?? ''),
            'image_alt' => trim($_POST['image_alt'] ?? ''),
            'icon_key' => $_POST['icon_key'] ?? 'package',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'seo_focus_keyword' => trim($_POST['seo_focus_keyword'] ?? ''),
        ];

        if (!empty($_FILES['image']['name'])) {
            $data['image'] = Upload::image($_FILES['image'], 'home');
        }

        // Extra data (JSON)
        if (!empty($_POST['extra_data'])) {
            $data['extra_data'] = $_POST['extra_data'];
        }

        $this->db->update('home_sections', $data, 'id = ?', [(int) $id]);
        logActivity('home_section_update', 'Ana sayfa bölümü güncellendi: ' . $data['title']);
        flash('success', 'Bölüm güncellendi.');
        redirect('/admin/ana-sayfa');
    }

    public function toggleStatus(string $id): void
    {
        Csrf::check();
        $section = $this->db->fetch("SELECT status FROM home_sections WHERE id = ?", [(int) $id]);
        $newStatus = $section['status'] === 'active' ? 'inactive' : 'active';
        $this->db->update('home_sections', ['status' => $newStatus], 'id = ?', [(int) $id]);
        flash('success', 'Durum güncellendi.');
        redirect('/admin/ana-sayfa');
    }
}
