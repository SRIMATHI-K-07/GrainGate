const RATE = 22;            // ₹ per kg
const OPENING_STOCK = 10000; // kg

// ---------- Data (localStorage-la save aagum) ----------
const defaultRecords = [
  { name: "Murugan", variety: "Ponni",  weight: 2000, moisture: 2 },
  { name: "Selvi",   variety: "ADT-37", weight: 1500, moisture: 3 },
  { name: "Karthik", variety: "Ponni",  weight: 1000, moisture: 1 }
];

function getRecords() {
  const saved = localStorage.getItem("records");
  return saved ? JSON.parse(saved) : defaultRecords;
}
function saveRecords(list) {
  localStorage.setItem("records", JSON.stringify(list));
}

// ---------- Calculation ----------
function calculate(weight, moisture) {
  const deduction = weight * moisture / 100;
  const net = weight - deduction;
  const amount = net * RATE;
  return { deduction, net, amount };
}

// ---------- Home page: stock cards ----------
function loadHome() {
  const box = document.getElementById("openStock");
  if (!box) return;
  const records = getRecords();
  let intake = 0;
  records.forEach(r => intake += calculate(r.weight, r.moisture).net);
  document.getElementById("openStock").innerText = OPENING_STOCK + " kg";
  document.getElementById("todayIntake").innerText = intake + " kg";
  document.getElementById("currentStock").innerText = (OPENING_STOCK + intake) + " kg";
}

// ---------- Stock page: table + search ----------
function loadTable() {
  const body = document.getElementById("stockBody");
  if (!body) return;
  body.innerHTML = "";
  let totalNet = 0, totalAmt = 0;
  getRecords().forEach(r => {
    const c = calculate(r.weight, r.moisture);
    totalNet += c.net; totalAmt += c.amount;
    body.innerHTML += `<tr>
      <td>${r.name}</td><td>${r.variety}</td><td>${r.weight}</td>
      <td>${r.moisture}%</td><td>${c.net}</td><td>₹${c.amount}</td></tr>`;
  });
  document.getElementById("totals").innerText =
    `Total net: ${totalNet} kg | Total paid: ₹${totalAmt}`;
}

function searchFarmer() {
  const text = document.getElementById("search").value.toLowerCase();
  document.querySelectorAll("#stockBody tr").forEach(row => {
    const name = row.cells[0].innerText.toLowerCase();
    row.style.display = name.includes(text) ? "" : "none";
  });
}

// ---------- Intake form: validate + calculate ----------
function handleIntake(e) {
  e.preventDefault();
  const name = document.getElementById("name").value.trim();
  const variety = document.getElementById("variety").value;
  const weight = parseFloat(document.getElementById("weight").value);
  const moisture = parseFloat(document.getElementById("moisture").value);
  let ok = true;

  document.getElementById("nameErr").innerText = "";
  document.getElementById("weightErr").innerText = "";
  document.getElementById("moistErr").innerText = "";

  if (name.length < 3) { document.getElementById("nameErr").innerText = "Name minimum 3 letters"; ok = false; }
  if (isNaN(weight) || weight <= 0) { document.getElementById("weightErr").innerText = "Weight 0-ku mela irukkanum"; ok = false; }
  if (isNaN(moisture) || moisture < 0 || moisture > 30) { document.getElementById("moistErr").innerText = "Moisture 0 to 30 mattum"; ok = false; }
  if (!ok) return;

  const c = calculate(weight, moisture);
  const records = getRecords();
  records.push({ name, variety, weight, moisture });
  saveRecords(records);

  const res = document.getElementById("result");
  res.style.display = "block";
  res.innerHTML = `Deduction: <b>${c.deduction} kg</b><br>
    Net weight: <b>${c.net} kg</b><br>
    Payable (₹${RATE}/kg): <b>₹${c.amount}</b><br>
    New stock: <b>${OPENING_STOCK + records.reduce((s, r) => s + calculate(r.weight, r.moisture).net, 0)} kg</b>`;
  e.target.reset();
}

// ---------- About page: contact form ----------
function handleContact(e) {
  e.preventDefault();
  const email = document.getElementById("email").value;
  const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!pattern.test(email)) { document.getElementById("emailErr").innerText = "Valid email kudunga"; return; }
  document.getElementById("emailErr").innerText = "";
  alert("Thank you! Message anuppiyaachu.");
  e.target.reset();
}

// ---------- Page load ----------
window.onload = function () {
  loadHome();
  loadTable();
  const intake = document.getElementById("intakeForm");
  if (intake) intake.addEventListener("submit", handleIntake);
  const contact = document.getElementById("contactForm");
  if (contact) contact.addEventListener("submit", handleContact);
};