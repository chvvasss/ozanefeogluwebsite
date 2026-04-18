import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import persist from "@alpinejs/persist";
import "htmx.org";
import { tiptapEditor } from "./tiptap-editor.js";

Alpine.plugin(focus);
Alpine.plugin(persist);

Alpine.data("tiptapEditor", tiptapEditor);

/* -----------------------------------------------------------------------
   Shared theme controller (same as public)
   ----------------------------------------------------------------------- */
Alpine.data("themeToggle", () => ({
  preference: Alpine.$persist("system").as("theme-pref"),
  resolved: "light",
  init() {
    this.apply();
    if (window.matchMedia) {
      window
        .matchMedia("(prefers-color-scheme: dark)")
        .addEventListener("change", () => this.apply());
    }
  },
  apply() {
    const sys = window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light";
    this.resolved = this.preference === "system" ? sys : this.preference;
    document.documentElement.dataset.theme = this.resolved;
  },
  cycle() {
    const order = ["system", "light", "dark"];
    const idx = order.indexOf(this.preference);
    this.preference = order[(idx + 1) % order.length];
    this.apply();
  },
}));

/* Sidebar toggle (mobile) */
Alpine.data("adminShell", () => ({
  sidebarOpen: false,
  toggleSidebar() {
    this.sidebarOpen = !this.sidebarOpen;
  },
  closeSidebar() {
    this.sidebarOpen = false;
  },
}));

/* Copy-to-clipboard helper for recovery codes */
Alpine.data("copyToClipboard", () => ({
  copied: false,
  async copy(text) {
    try {
      await navigator.clipboard.writeText(text);
      this.copied = true;
      setTimeout(() => (this.copied = false), 1500);
    } catch {
      this.copied = false;
    }
  },
}));

/* HTMX configuration — CSRF header from meta */
document.body.addEventListener("htmx:configRequest", (event) => {
  const token = document.querySelector('meta[name="csrf-token"]');
  if (token) {
    event.detail.headers["X-CSRF-Token"] = token.content;
  }
});

window.Alpine = Alpine;
Alpine.start();
