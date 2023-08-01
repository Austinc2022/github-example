// import { startCase, isArray } from "lodash-es";

/**
 * Return a new datatable column which can be used for chaining
 */
export function column(name = "id", label) {
  return new DatatableColumn(name, label);
}

/**
 * Structure to represent the column information to be fed into the datatable
 */
class DatatableColumn {
  constructor(field, label = "") {
    this.field = field;
    this.label = label || startCase(field);
    this.align = "left";
    this.headerStyle = undefined;
    this.classes = [];
    this.sortable = true;
    this.visible = true;

    this.format = undefined;
    this.renderComponent = null;
  }

  /**
   * Center align this column
   */
  setCenter() {
    this.align = "center";
    this.classes.push("text-center");
    return this;
  }

  /**
   * Set custom styling to apply to the column header <th>
   */
  setHeaderStyle(key, value) {
    if (!this.headerStyle) this.headerStyle = {};
    this.headerStyle[key] = value;
    return this;
  }

  /**
   * Set a class or array of classes to apply to the column (both <th> and <td>)
   */
  setClasses(classes) {
    this.classes = isArray(classes) ? classes : [classes];
    return this;
  }

  /**
   * Disable sorting on this column (useful for calculated clientside only columns)
   */
  setUnsortable() {
    this.sortable = false;
    return this;
  }

  /**
   * Toggle whether the entire column should be visible
   */
  setVisible(isVisible) {
    this.visible = isVisible;
    return this;
  }
}
