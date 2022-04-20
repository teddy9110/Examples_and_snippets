describe("Add to cart", () => {
  it("Visits staging website", () => {
    cy.visit("https://rh-fitness-site-teamrh-teamrhfitnes-teamrh.vercel.app/");

    cy.get(".button").contains("Add To Cart").click({ force: true });
    cy.wait(500);
    cy.get("#closeDialog").click();
    cy.wait(200);
    cy.location().should((loc) => {
      expect(loc.href).to.eq(
        "https://rh-fitness-site-teamrh-teamrhfitnes-teamrh.vercel.app/store/cart"
      );
    });
    cy.get("h3").contains("Team RH Life Plan - 12 Month Contract");
  });
});
