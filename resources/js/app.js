import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import persist from "@alpinejs/persist";

Alpine.plugin(focus);
Alpine.plugin(persist);

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
