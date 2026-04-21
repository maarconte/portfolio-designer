# Jeanne — WordPress Portfolio Theme

A minimalist black-and-white WordPress theme for graphic designers. Projects are displayed as cards in a full-page horizontal slider; clicking a card opens a full-screen lightbox gallery.

---

## Requirements

- WordPress 6.0+
- PHP 8.0+
- No page-builder plugins required

---

## Installation

1. Copy the `jeanne` folder into `wp-content/themes/`.
2. In the WordPress admin go to **Appearance → Themes** and activate **Jeanne**.
3. Go to **Settings → Permalinks** and click **Save Changes** to flush rewrite rules (required for the `project` post type).

---

## Adding Projects

1. In the WordPress admin, click **Projects → Add New**.
2. Set the **Title** (displayed on the card and in the modal).
3. Write a **Description** in the editor (optional; shown in the modal info strip).
4. Set a **Featured Image** — this is the thumbnail shown on the slider card.
5. In the **Project Gallery** meta box, click **Add / Edit Gallery Images** and select one or more images from the Media Library. These are the images shown inside the lightbox. Drag to reorder.
6. Fill in optional **Project Details** (Year, Client, Category) in the sidebar meta box.
7. Use **Attributes → Order** to control the display order of cards on the homepage.
8. Publish.

---

## Customizer Options

Go to **Appearance → Customize**:

| Setting | Description |
|---|---|
| **Site Identity → Site Title** | Displayed in the top-left header |
| **Site Identity → Logo** | Upload a logo (replaces the text title) |
| **Portfolio → Slider Autoplay Delay** | Time in ms between auto-advances. Set to `0` to disable |
| **Portfolio → Number of Projects** | How many projects to show. `-1` = all |
| **Portfolio → Footer Text** | Custom footer copy (HTML allowed) |
| **Typography → Google Font Name** | Enter a Google Fonts family name, e.g. `DM Sans` |

---

## Navigation

Register a menu at **Appearance → Menus**. Assign it to the **Primary Menu** location. It appears top-right on all pages and in the footer.

---

## Slider Interaction

- **Hover left 18 %** of the slider → cursor becomes `←`, click navigates to the previous project.
- **Hover right 18 %** of the slider → cursor becomes `→`, click navigates to the next project.
- **Hover a project image** → cursor shows `More`.
- **Click a project card** → opens the lightbox.
- **Keyboard** → `←` / `→` arrows navigate the slider (when the modal is closed) or the gallery (when the modal is open). `Escape` closes the modal.
- **Touch/mobile** → swipe left/right to navigate the slider or the gallery inside the modal. Arrow buttons appear at the bottom on small screens.
- **Autoplay** pauses when the mouse enters the slider and resumes on leave.

---

## File Structure

```
jeanne/
├── style.css                  Theme declaration
├── functions.php              Bootstrap, theme support, image sizes
├── front-page.php             Homepage (slider + modal)
├── single-project.php         Standalone project page (SEO-accessible URL)
├── index.php                  Blog / fallback
├── page.php                   Static pages
├── 404.php                    Error page
├── header.php                 Site header
├── footer.php                 Site footer
├── inc/
│   ├── post-types.php         Registers the 'project' CPT
│   ├── meta-boxes.php         Gallery + details meta boxes
│   ├── customizer.php         Customizer controls
│   └── enqueue.php            Script & style registration
└── assets/
    ├── css/
    │   └── main.css           All theme styles
    └── js/
        ├── main.js            Cursor + slider + modal
        ├── admin-gallery.js   Admin media uploader for gallery
        └── customizer-preview.js  Live preview bindings
```

---

## Extending the Theme

**Adding a second image size for cards:** In `functions.php`, adjust `add_image_size('jeanne-card', ...)`. Then regenerate thumbnails with a plugin like *Regenerate Thumbnails*.

**Adding project taxonomies (e.g. Tags):** In `inc/post-types.php`, call `register_taxonomy()` and add it to the `project` post type.

**Changing the card width:** In `assets/css/main.css`, update `--card-width` in `:root`. Use the responsive breakpoints lower in the file for smaller screens.

**Custom cursor color:** The `.custom-cursor__text` block in `main.css` controls the `More` label background. The arrow inherits `color`.

---

## Accessibility Notes

- Project cards have `role="button"` and `tabindex="0"` so keyboard users can open modals.
- The modal has `role="dialog"` and `aria-modal="true"`.
- Focus is sent to the close button when a modal opens.
- All interactive elements have `:focus-visible` outlines.
- `prefers-reduced-motion` disables CSS transitions.
- The standalone `/project/slug/` URL provides a fully accessible, no-JS path to every project.

---

## License

GNU General Public License v2 or later — https://www.gnu.org/licenses/gpl-2.0.html
