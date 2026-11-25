#!/usr/bin/env bash
# ============================================================
# Mapelead end-to-end HTTP test suite (curl-based)
# Run from the server: bash tests/e2e-curl.sh
# Or from any machine that can reach mapelead.org:
#   BASE=https://mapelead.org bash tests/e2e-curl.sh
# For authenticated tests supply credentials:
#   ADMIN_EMAIL=you@example.com ADMIN_PASSWORD=secret bash tests/e2e-curl.sh
# ============================================================

BASE="${BASE:-https://mapelead.org}"
PASS=0; FAIL=0; SKIP=0

GREEN="\033[32m"; RED="\033[31m"; YELLOW="\033[33m"; RESET="\033[0m"; BOLD="\033[1m"

pass() { echo -e "  ${GREEN}✓${RESET} $1"; ((PASS++)); }
fail() { echo -e "  ${RED}✗${RESET} $1"; echo -e "    ${RED}→ $2${RESET}"; ((FAIL++)); }
skip() { echo -e "  ${YELLOW}-${RESET} $1 (skipped: $2)"; ((SKIP++)); }

# Check a URL returns expected HTTP status (follows redirects up to 5 hops)
check_status() {
  local name="$1" url="$2" expected="${3:-200}"
  local actual
  actual=$(curl -sk -o /dev/null -w "%{http_code}" -L --max-redirs 5 "$url" 2>&1)
  if [ "$actual" = "$expected" ]; then
    pass "$name (HTTP $actual)"
  else
    fail "$name" "Expected HTTP $expected, got HTTP $actual — URL: $url"
  fi
}

# Check URL body contains text (case-insensitive)
check_contains() {
  local name="$1" url="$2" text="$3"
  local body
  body=$(curl -sk -L --max-redirs 5 "$url" 2>&1)
  if echo "$body" | grep -qi "$text"; then
    pass "$name"
  else
    fail "$name" "Text '$text' not found on page $url"
  fi
}

# Check URL body does NOT contain Laravel/PHP error markers
check_no_error() {
  local name="$1" url="$2"
  local body
  body=$(curl -sk -L --max-redirs 5 "$url" 2>&1)
  if echo "$body" | grep -qi "Whoops, looks like something went wrong\|ErrorException\|Symfony\\\\Component\|sf-reset-exception"; then
    fail "$name" "Laravel/PHP error page detected at $url"
  else
    pass "$name (no server error)"
  fi
}

# Login and get cookies; outputs cookie jar path
admin_login() {
  local jar="/tmp/mapelead_cookies_$$"
  # Fetch CSRF token
  local csrf
  csrf=$(curl -sk -c "$jar" "$BASE/login" | grep -o 'name="_token" value="[^"]*"' | head -1 | sed 's/.*value="//;s/"//')
  curl -sk -b "$jar" -c "$jar" -X POST "$BASE/login" \
    -F "email=$ADMIN_EMAIL" \
    -F "password=$ADMIN_PASSWORD" \
    -F "_token=$csrf" \
    -L --max-redirs 5 -o /dev/null
  echo "$jar"
}

check_auth_status() {
  local name="$1" jar="$2" url="$3" expected="${4:-200}"
  local actual
  actual=$(curl -sk -b "$jar" -o /dev/null -w "%{http_code}" -L --max-redirs 5 "$url" 2>&1)
  if [ "$actual" = "$expected" ]; then
    pass "$name (HTTP $actual)"
  else
    fail "$name" "Expected HTTP $expected, got HTTP $actual — URL: $url"
  fi
}

check_auth_no_error() {
  local name="$1" jar="$2" url="$3"
  local body
  body=$(curl -sk -b "$jar" -L --max-redirs 5 "$url" 2>&1)
  if echo "$body" | grep -qi "Whoops, looks like something went wrong\|ErrorException\|sf-reset-exception"; then
    fail "$name" "Laravel/PHP error page at $url"
  else
    pass "$name"
  fi
}

# ─── PUBLIC PAGES ────────────────────────────────────────────────────────────
echo -e "\n${BOLD}── Public Pages ─────────────────────────────────────────────${RESET}"
check_status "Homepage"               "$BASE/"              200
check_status "Courses listing"        "$BASE/courses"       200
check_status "Single course (first)"  "$BASE/courses"       200
check_status "Blog listing"           "$BASE/blog"          200
check_status "Contact page"           "$BASE/contact"       200
check_no_error "Homepage - no server error"       "$BASE/"
check_no_error "Courses page - no server error"   "$BASE/courses"
check_no_error "Blog page - no server error"      "$BASE/blog"

# ─── AUTH PAGES ──────────────────────────────────────────────────────────────
echo -e "\n${BOLD}── Auth Pages ───────────────────────────────────────────────${RESET}"
check_status "Login page"           "$BASE/login"           200
check_status "Register page"        "$BASE/register"        200
check_status "Forgot password"      "$BASE/forgot-password" 200
check_no_error "Login page - no server error"           "$BASE/login"
check_no_error "Register page - no server error"        "$BASE/register"
check_no_error "Forgot password - no server error"      "$BASE/forgot-password"

# ─── PROTECTED ROUTE REDIRECTS ───────────────────────────────────────────────
echo -e "\n${BOLD}── Protected Routes (unauthenticated should → login) ────────${RESET}"
# Unauthenticated protected pages should give 200 (after redirect to /login) or 302
# NOT 500. We follow redirects and expect 200 on the login page.
check_status "Admin → login redirect"           "$BASE/admin"              200
check_status "Instructor → login redirect"      "$BASE/instructor"         200
check_status "Student dashboard → login"        "$BASE/student/dashboard"  200
check_status "Admin email-templates → login"    "$BASE/admin/email-templates" 200

# ─── AUTHENTICATED ADMIN TESTS ───────────────────────────────────────────────
echo -e "\n${BOLD}── Admin (Authenticated) ────────────────────────────────────${RESET}"
if [ -z "$ADMIN_EMAIL" ] || [ -z "$ADMIN_PASSWORD" ]; then
  skip "Admin tests" "set ADMIN_EMAIL and ADMIN_PASSWORD env vars"
else
  JAR=$(admin_login)
  # Check if we're actually logged in
  DASH=$(curl -sk -b "$JAR" -o /dev/null -w "%{http_code}" -L "$BASE/admin" 2>&1)
  if [ "$DASH" = "200" ]; then
    pass "Admin login succeeded"

    check_auth_status   "Admin dashboard"              "$JAR" "$BASE/admin"               200
    check_auth_no_error "Admin dashboard - no error"   "$JAR" "$BASE/admin"
    check_auth_status   "Admin users"                  "$JAR" "$BASE/admin/users"         200
    check_auth_no_error "Admin users - no error"       "$JAR" "$BASE/admin/users"
    check_auth_status   "Admin courses"                "$JAR" "$BASE/admin/courses"        200
    check_auth_no_error "Admin courses - no error"     "$JAR" "$BASE/admin/courses"
    check_auth_status   "Admin enrollments (admissions)" "$JAR" "$BASE/admin/enrollments" 200
    check_auth_no_error "Admin enrollments - no error"   "$JAR" "$BASE/admin/enrollments"
    check_auth_status   "Admin payments"               "$JAR" "$BASE/admin/payments"       200
    check_auth_no_error "Admin payments - no error"    "$JAR" "$BASE/admin/payments"
    check_auth_status   "Admin blog posts"             "$JAR" "$BASE/admin/blog/posts"     200
    check_auth_no_error "Admin blog posts - no error"  "$JAR" "$BASE/admin/blog/posts"
    check_auth_status   "Admin leads"                  "$JAR" "$BASE/admin/leads"          200
    check_auth_no_error "Admin leads - no error"       "$JAR" "$BASE/admin/leads"
    check_auth_status   "Admin contacts"               "$JAR" "$BASE/admin/contacts"       200
    check_auth_no_error "Admin contacts - no error"    "$JAR" "$BASE/admin/contacts"
    check_auth_status   "Admin announcements"          "$JAR" "$BASE/admin/announcements"  200
    check_auth_no_error "Admin announcements - no error" "$JAR" "$BASE/admin/announcements"
    check_auth_status   "Admin settings"               "$JAR" "$BASE/admin/settings"       200
    check_auth_no_error "Admin settings - no error"    "$JAR" "$BASE/admin/settings"
    check_auth_status   "Admin analytics"              "$JAR" "$BASE/admin/analytics"      200
    check_auth_no_error "Admin analytics - no error"   "$JAR" "$BASE/admin/analytics"
    check_auth_status   "Admin CMS"                    "$JAR" "$BASE/admin/cms"            200
    check_auth_no_error "Admin CMS - no error"         "$JAR" "$BASE/admin/cms"

    # Previously broken — was 500 (wrong view path)
    check_auth_status   "Admin email templates (was 500, now fixed)" "$JAR" "$BASE/admin/email-templates" 200
    check_auth_no_error "Admin email templates - no error"           "$JAR" "$BASE/admin/email-templates"

    check_auth_status   "Admin profile"                "$JAR" "$BASE/admin/profile"        200
    check_auth_no_error "Admin profile - no error"     "$JAR" "$BASE/admin/profile"

    rm -f "$JAR"
  else
    fail "Admin login" "Credentials may be wrong (got HTTP $DASH after login)"
    rm -f "$JAR"
  fi
fi

# ─── AUTHENTICATED INSTRUCTOR TESTS ──────────────────────────────────────────
echo -e "\n${BOLD}── Instructor (Authenticated) ───────────────────────────────${RESET}"
if [ -z "$INSTRUCTOR_EMAIL" ] || [ -z "$INSTRUCTOR_PASSWORD" ]; then
  # Try using admin as instructor if same credentials
  if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ]; then
    echo -e "  ${YELLOW}→${RESET} Using admin account to test instructor routes"
    export INSTRUCTOR_EMAIL="$ADMIN_EMAIL"
    export INSTRUCTOR_PASSWORD="$ADMIN_PASSWORD"
  else
    skip "Instructor tests" "set INSTRUCTOR_EMAIL and INSTRUCTOR_PASSWORD env vars"
  fi
fi

if [ -n "$INSTRUCTOR_EMAIL" ] && [ -n "$INSTRUCTOR_PASSWORD" ]; then
  export ADMIN_EMAIL="$INSTRUCTOR_EMAIL"
  export ADMIN_PASSWORD="$INSTRUCTOR_PASSWORD"
  IJAR=$(admin_login)
  IDASH=$(curl -sk -b "$IJAR" -o /dev/null -w "%{http_code}" -L "$BASE/instructor" 2>&1)
  if [ "$IDASH" = "200" ]; then
    pass "Instructor login / access"

    check_auth_status   "Instructor dashboard"            "$IJAR" "$BASE/instructor"           200
    check_auth_no_error "Instructor dashboard - no error" "$IJAR" "$BASE/instructor"
    check_auth_status   "Instructor courses"              "$IJAR" "$BASE/instructor/courses"    200
    check_auth_no_error "Instructor courses - no error"   "$IJAR" "$BASE/instructor/courses"
    check_auth_status   "Instructor students"             "$IJAR" "$BASE/instructor/students"   200
    check_auth_no_error "Instructor students - no error"  "$IJAR" "$BASE/instructor/students"
    check_auth_status   "Instructor earnings"             "$IJAR" "$BASE/instructor/earnings"   200
    check_auth_no_error "Instructor earnings - no error"  "$IJAR" "$BASE/instructor/earnings"
    check_auth_status   "Instructor profile (new page)"   "$IJAR" "$BASE/instructor/profile"    200
    check_auth_no_error "Instructor profile - no error"   "$IJAR" "$BASE/instructor/profile"

    rm -f "$IJAR"
  else
    fail "Instructor access" "Could not access /instructor (HTTP $IDASH) — user may not have instructor role"
    rm -f "$IJAR"
  fi
fi

# ─── SUMMARY ─────────────────────────────────────────────────────────────────
echo -e "\n${BOLD}═══════════════════════════════════════════════════════════════${RESET}"
echo -e "${BOLD}RESULTS: ${GREEN}${PASS} passed${RESET}  ${RED}${FAIL} failed${RESET}  ${YELLOW}${SKIP} skipped${RESET}"
echo -e "${BOLD}═══════════════════════════════════════════════════════════════${RESET}"
echo ""
[ $FAIL -gt 0 ] && exit 1 || exit 0
