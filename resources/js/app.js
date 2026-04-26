import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import persist from "@alpinejs/persist";

Alpine.plugin(focus);
Alpine.plugin(persist);

// Theme toggle — light/dark only (no system follow-along).
Alpine.data("themeToggle", () => ({
  preference: Alpine.$persist("light").as("theme-pref"),
  resolved: "light",

  init() {
    // Migrate legacy "system" preference → resolve once + persist explicit choice.
    if (this.preference === "system") {
      const sysDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
      this.preference = sysDark ? "dark" : "light";
    }
    this.apply();
  },

  apply() {
    if (this.preference !== "light" && this.preference !== "dark") {
      this.preference = "light";
    }
    this.resolved = this.preference;
    document.documentElement.dataset.theme = this.resolved;
  },

  cycle() {
    this.preference = this.preference === "dark" ? "light" : "dark";
    this.apply();
  },
}));

Alpine.data("mobileNav", () => ({
  open: false,
  toggle() {
    this.open = !this.open;
    document.documentElement.style.overflow = this.open ? "hidden" : "";
  },
  close() {
    this.open = false;
    document.documentElement.style.overflow = "";
  },
}));

Alpine.data("copyHandle", (handle) => ({
  copied: false,
  async copy() {
    try {
      await navigator.clipboard.writeText(handle);
      this.copied = true;
      setTimeout(() => (this.copied = false), 1600);
    } catch {
      this.copied = false;
    }
  },
}));

window.Alpine = Alpine;
Alpine.start();
