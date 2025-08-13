--
-- PostgreSQL database dump
--

-- Dumped from database version 13.10
-- Dumped by pg_dump version 13.10

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: companies; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.companies (
    company character varying(10) NOT NULL,
    name character varying(60) NOT NULL,
    active boolean DEFAULT true NOT NULL,
    created_by character varying(20),
    updated_by character varying(20),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.companies OWNER TO postgres;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: inv_hist; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_hist (
    id bigint NOT NULL,
    inv_ref character varying(30),
    inv_date date NOT NULL,
    site_id character varying(10) NOT NULL,
    loc_id character varying(10) NOT NULL,
    type character varying(255) NOT NULL,
    item_id bigint NOT NULL,
    qty double precision NOT NULL,
    price double precision NOT NULL,
    total_price double precision NOT NULL,
    remark character varying(255),
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT inv_hist_type_check CHECK (((type)::text = ANY ((ARRAY['IN'::character varying, 'OUT'::character varying, 'IA_IN'::character varying, 'IA_OUT'::character varying])::text[])))
);


ALTER TABLE public.inv_hist OWNER TO postgres;

--
-- Name: inv_hist_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_hist_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_hist_id_seq OWNER TO postgres;

--
-- Name: inv_hist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_hist_id_seq OWNED BY public.inv_hist.id;


--
-- Name: inventory; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inventory (
    site_id character varying(10) NOT NULL,
    loc_id character varying(10) NOT NULL,
    item_id bigint NOT NULL,
    qty double precision DEFAULT '0'::double precision,
    avg_price double precision DEFAULT '0'::double precision,
    total_price double precision DEFAULT '0'::double precision
);


ALTER TABLE public.inventory OWNER TO postgres;

--
-- Name: items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.items (
    id bigint NOT NULL,
    code character varying(10) NOT NULL,
    name character varying(60) NOT NULL,
    uom_id character varying(10) NOT NULL,
    type character varying(10) NOT NULL,
    "desc" character varying(255),
    active boolean DEFAULT true NOT NULL,
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.items OWNER TO postgres;

--
-- Name: items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.items_id_seq OWNER TO postgres;

--
-- Name: items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.items_id_seq OWNED BY public.items.id;


--
-- Name: locations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.locations (
    loc_id character varying(10) NOT NULL,
    site_id character varying(10) NOT NULL,
    name character varying(60) NOT NULL,
    active boolean DEFAULT true NOT NULL,
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.locations OWNER TO postgres;

--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO postgres;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO postgres;

--
-- Name: modules; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.modules (
    id bigint NOT NULL,
    name character varying(100) NOT NULL,
    "desc" character varying(150),
    icon character varying(100),
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status boolean DEFAULT true NOT NULL,
    parent character varying(10),
    "order" numeric(8,0) NOT NULL,
    code character varying(20) NOT NULL,
    module_name character varying(20) DEFAULT '#'::character varying NOT NULL,
    superuser boolean DEFAULT false NOT NULL
);


ALTER TABLE public.modules OWNER TO postgres;

--
-- Name: modules_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.modules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.modules_id_seq OWNER TO postgres;

--
-- Name: modules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.modules_id_seq OWNED BY public.modules.id;


--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_resets (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_resets OWNER TO postgres;

--
-- Name: permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permissions OWNER TO postgres;

--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permissions_id_seq OWNER TO postgres;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: po_dtl; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.po_dtl (
    po_id character varying(30) NOT NULL,
    line integer NOT NULL,
    item_id character varying(10) NOT NULL,
    qty integer NOT NULL,
    price_each double precision NOT NULL,
    discount_each double precision NOT NULL,
    price_total double precision NOT NULL,
    remark character varying(255)
);


ALTER TABLE public.po_dtl OWNER TO postgres;

--
-- Name: po_mstr; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.po_mstr (
    po_id character varying(30) NOT NULL,
    sup_id bigint NOT NULL,
    po_date date NOT NULL,
    status character varying(255) NOT NULL,
    remark character varying(255),
    site_created character varying(10) NOT NULL,
    line_count integer NOT NULL,
    item_count integer NOT NULL,
    total_price double precision NOT NULL,
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT po_mstr_status_check CHECK (((status)::text = ANY ((ARRAY['CREATED'::character varying, 'POST'::character varying, 'CLOSED'::character varying, 'CANCEL'::character varying])::text[])))
);


ALTER TABLE public.po_mstr OWNER TO postgres;

--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO postgres;

--
-- Name: role_mstr_role_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.role_mstr_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.role_mstr_role_id_seq OWNER TO postgres;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status boolean DEFAULT true NOT NULL,
    role_name character varying(20) NOT NULL,
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: site_user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.site_user (
    user_id bigint NOT NULL,
    site character varying(10) NOT NULL,
    "default" boolean DEFAULT false NOT NULL
);


ALTER TABLE public.site_user OWNER TO postgres;

--
-- Name: sites; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sites (
    site character varying(10) NOT NULL,
    name character varying(60) NOT NULL,
    company_id character varying(10) NOT NULL,
    active boolean DEFAULT true NOT NULL,
    created_by character varying(20),
    updated_by character varying(20),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    site_company boolean DEFAULT false NOT NULL
);


ALTER TABLE public.sites OWNER TO postgres;

--
-- Name: suppliers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.suppliers (
    id bigint NOT NULL,
    code character varying(10) NOT NULL,
    name character varying(60) NOT NULL,
    phone1 character varying(25) NOT NULL,
    phone2 character varying(25),
    address character varying(255) NOT NULL,
    "desc" character varying(255),
    active boolean DEFAULT true NOT NULL,
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.suppliers OWNER TO postgres;

--
-- Name: suppliers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.suppliers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.suppliers_id_seq OWNER TO postgres;

--
-- Name: suppliers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.suppliers_id_seq OWNED BY public.suppliers.id;


--
-- Name: uom; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.uom (
    id bigint NOT NULL,
    code character varying(10) NOT NULL,
    name character varying(60) NOT NULL,
    short_name character varying(10) NOT NULL,
    active boolean DEFAULT true NOT NULL,
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.uom OWNER TO postgres;

--
-- Name: uom_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.uom_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.uom_id_seq OWNER TO postgres;

--
-- Name: uom_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.uom_id_seq OWNED BY public.uom.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    user_id bigint NOT NULL,
    username character varying(20) NOT NULL,
    name character varying(150) NOT NULL,
    password character varying(255) NOT NULL,
    role_id bigint NOT NULL,
    remember_token character varying(100),
    created_by character varying(20) NOT NULL,
    updated_by character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status boolean DEFAULT true NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_user_id_seq OWNER TO postgres;

--
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_user_id_seq OWNED BY public.users.user_id;


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: inv_hist id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_hist ALTER COLUMN id SET DEFAULT nextval('public.inv_hist_id_seq'::regclass);


--
-- Name: items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.items ALTER COLUMN id SET DEFAULT nextval('public.items_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: modules id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.modules ALTER COLUMN id SET DEFAULT nextval('public.modules_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: suppliers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suppliers ALTER COLUMN id SET DEFAULT nextval('public.suppliers_id_seq'::regclass);


--
-- Name: uom id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.uom ALTER COLUMN id SET DEFAULT nextval('public.uom_id_seq'::regclass);


--
-- Name: users user_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN user_id SET DEFAULT nextval('public.users_user_id_seq'::regclass);


--
-- Data for Name: companies; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.companies (company, name, active, created_by, updated_by, created_at, updated_at) FROM stdin;
GEJ 2	PT. Gadai Elektronik Jakarta 2	t	\N	\N	2023-07-17 09:52:51	2023-07-17 09:52:51
AG2	PT. Amanah Terima Gadai 2	t	\N	\N	2023-07-17 09:52:51	2023-07-17 09:52:51
AG1	PT. Amanah Terima Gadai	t	\N	\N	2023-07-17 09:50:39	2023-07-17 09:52:51
GJB	PT. Gadai Jadi Berkah	t	\N	\N	2023-07-17 09:50:39	2023-07-17 09:52:51
GM1	PT. GADAI MENUJU SUKSES	t	\N	\N	2023-07-17 09:52:51	2023-07-17 09:52:51
KDG	PT. KUSUMA DWIPA GADAI	t	\N	\N	2023-07-17 09:52:51	2023-07-17 09:52:51
SAG	PT. Semeru Agung Gadai	t	\N	\N	2023-07-17 09:52:51	2023-07-17 09:52:51
GEJ	PT. Gadai Elektronik Jakarta	t	\N	\N	2023-07-17 09:50:39	2023-07-17 09:52:51
IJG1	PT. INDAH JAYA GADAI	t	\N	\N	2023-07-17 09:52:51	2023-07-17 09:52:51
SIG	PT. SETIA INDAH GADAI	t	\N	\N	2023-07-17 09:52:51	2023-07-17 09:52:51
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: inv_hist; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_hist (id, inv_ref, inv_date, site_id, loc_id, type, item_id, qty, price, total_price, remark, created_by, updated_by, created_at, updated_at) FROM stdin;
2	IAI2307H01-0001	2023-07-28	H01	H01-WH01	IA_IN	1	10	120000	1200000	stock awal	2109049	2109049	2023-07-28 17:01:04	2023-07-28 17:01:04
3	IAI2307H01-0002	2023-07-28	H01	H01-WH01	IA_IN	2	5	150000	750000	Stock awal	2109049	2109049	2023-07-28 17:01:28	2023-07-28 17:01:28
4	IAI2307H01-0003	2023-07-28	H01	H01-WH01	IA_IN	2	10	140000	1400000	tambahan	2109049	2109049	2023-07-28 17:01:47	2023-07-28 17:01:47
5	IAI2307H01-0004	2023-07-29	H01	H01-WH02	IA_IN	1	10	125000	1250000	\N	2109049	2109049	2023-07-29 11:00:42	2023-07-29 11:00:42
6	IAO2307H01-0001	2023-07-29	H01	H01-WH01	IA_OUT	1	2	120000	240000	testing	2109049	2109049	2023-07-29 11:40:25	2023-07-29 11:40:25
7	IAO2307H01-0002	2023-07-29	H01	H01-WH01	IA_OUT	1	1	120000	120000	test	2109049	2109049	2023-07-29 11:43:50	2023-07-29 11:43:50
8	IAI2308H01-0005	2023-08-01	H01	H01-WH02	IA_IN	1	5	100000	500000	\N	2109049	2109049	2023-08-01 15:12:06	2023-08-01 15:12:06
\.


--
-- Data for Name: inventory; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inventory (site_id, loc_id, item_id, qty, avg_price, total_price) FROM stdin;
H01	H01-WH01	2	15	143333.33333333	2150000
H01	H01-WH01	1	7	120000	840000
H01	H01-WH02	1	15	116666.66666667	1750000
\.


--
-- Data for Name: items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.items (id, code, name, uom_id, type, "desc", active, created_by, updated_by, created_at, updated_at) FROM stdin;
1	PR-001	CCTV indoor	1	PRODUCT	Indoor CCTV tanpa microphone	t	2109049	2109049	2023-07-11 10:08:45	2023-07-11 10:16:17
2	PR-002	SBG PT. ATG	3	PRODUCT	SBG 1 rim untuk PT. ATG	t	2109049	2109049	2023-07-11 10:17:50	2023-07-11 10:18:16
\.


--
-- Data for Name: locations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.locations (loc_id, site_id, name, active, created_by, updated_by, created_at, updated_at) FROM stdin;
H01-WH01	H01	Gudang ATK	t	2109049	2109049	2023-07-28 17:00:09	2023-07-28 17:00:09
H01-WH02	H01	Gudang Printer	t	2109049	2109049	2023-07-28 17:00:15	2023-07-28 17:00:15
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_resets_table	1
3	2019_08_19_000000_create_failed_jobs_table	1
4	2019_12_14_000001_create_personal_access_tokens_table	1
5	2023_06_18_212022_create_roles_table	1
6	2023_06_18_221416_update_users_table	1
7	2023_06_22_111010_create_permission_tables	1
8	2023_06_22_125127_update_roles_table	1
11	2023_06_23_000706_create_modules_table	2
13	2023_06_23_142406_update_modules_table	3
14	2023_06_23_170507_update_code_modules_table	4
23	2023_06_24_094036_modules_update_table	5
24	2023_07_07_151941_create_site_table	6
27	2023_07_07_155246_create_uom_table	7
29	2023_07_08_110312_create_items_table	8
32	2023_07_11_110156_create_suppliers_table	9
39	2023_07_13_164312_create_sites_table	11
40	2023_07_17_093442_update_sites_table	12
43	2023_07_17_113200_create_site_user_table	13
49	2023_07_11_140543_create_po_mstr_table	14
64	2023_07_26_095413_create_inventory_table	15
\.


--
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.model_has_permissions (permission_id, model_type, model_id) FROM stdin;
\.


--
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.model_has_roles (role_id, model_type, model_id) FROM stdin;
2	App\\Models\\User	2
1	App\\Models\\User	1
3	App\\Models\\User	3
\.


--
-- Data for Name: modules; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.modules (id, name, "desc", icon, created_by, updated_by, created_at, updated_at, status, parent, "order", code, module_name, superuser) FROM stdin;
5	Setting	Master setting	fa fa-cogs	2109049	2109049	2023-07-01 12:03:49	2023-07-06 11:48:26	t	\N	0	HDR_001	#	f
14	Purchase Order	Header PR	fas fa-shopping-basket	2109049	2109049	2023-07-11 12:13:14	2023-07-11 12:17:44	t	\N	1	HDR_PR	#	f
8	Master site	Master site	fa fa-store	2109049	2109049	2023-07-07 15:12:32	2023-07-17 10:14:13	t	15	1	MNU_004	sites	t
16	Company	Master company	fa fa-university	2109049	2109049	2023-07-17 10:14:07	2023-07-17 10:27:06	t	15	0	MNU_COM	companies	t
6	Roles	Master roles	fa fa-briefcase	2109049	2109049	2023-07-05 11:40:07	2023-07-07 15:59:55	t	9	1	MNU_003	roles	f
15	Site Settings	Header sites	fa fa-store-alt	2109049	2109049	2023-07-17 10:12:36	2023-07-17 10:30:16	t	5	4	HDR_SITE	#	t
18	Inventory IN	Inventory adjust in	\N	2109049	2109049	2023-07-26 09:50:24	2023-07-26 09:50:24	t	17	0	MNU_IA_IN	inv_adj_in	f
19	Inventory OUT	Inventory adjust out	\N	2109049	2109049	2023-07-26 09:50:59	2023-07-26 09:50:59	t	17	1	MNU_IA_OUT	inv_adj_out	f
20	Location	Master location	fas fa-warehouse	2109049	2109049	2023-07-28 09:28:08	2023-07-28 09:31:20	t	15	2	MNU_LOC	location	f
17	Inventory Adjustment	Header Inventory Adjustment	fas fa-exchange-alt	2109049	2109049	2023-07-26 09:49:34	2023-07-28 09:46:06	t	\N	2	HDR_IA	#	f
22	Inventory Report	Inventory report	\N	2109049	2109049	2023-07-29 11:49:42	2023-07-29 11:50:40	t	21	0	MNU_INV_RPT	inv_report	f
21	Laporan	Header report	fa fa-book	2109049	2109049	2023-07-29 11:48:53	2023-07-31 13:02:14	t	\N	5	HDR_RPT	#	f
10	Master Item	Master item header	fa fa-box-open	2109049	2109049	2023-07-07 16:32:04	2023-07-07 16:34:28	t	5	2	HDR_003	#	f
2	Module	Module tab	fas fa-desktop	2109049	2109049	2023-06-24 09:35:05	2023-07-05 15:56:49	t	5	0	MNU_001	modules	t
9	Employee	Employee header	fa fa-users-cog	2109049	2109049	2023-07-07 15:59:09	2023-07-11 09:29:16	t	5	1	HDR_002	#	f
3	Users	Master users	fa fa-users	2109049	2109049	2023-06-24 09:50:15	2023-07-11 09:29:30	t	9	0	MNU_002	users	f
13	Supplier	Master supplier	fas fa-store-alt	2109049	2109049	2023-07-11 10:59:06	2023-07-11 10:59:06	t	10	0	MNU_007	supplier	f
11	UoM	Unit of Measure	fa fa-weight-hanging	2109049	2109049	2023-07-07 16:33:24	2023-07-11 10:59:34	t	10	2	MNU_005	uom	f
12	Items	Master item	fa fa-boxes	2109049	2109049	2023-07-07 16:34:12	2023-07-11 10:59:39	t	10	1	MNU_006	item	f
4	Purchase Order	Purchase Order	\N	2109049	2109049	2023-06-30 13:16:01	2023-07-11 12:13:51	t	14	0	MNU_008	purchase_order	f
\.


--
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_resets (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.permissions (id, name, guard_name, created_at, updated_at) FROM stdin;
2	MNU_001_view	web	2023-06-24 09:34:00	2023-06-24 09:34:00
3	MNU_001_create	web	2023-06-24 09:34:00	2023-06-24 09:34:00
4	MNU_001_update	web	2023-06-24 09:34:00	2023-06-24 09:34:00
5	MNU_001_print	web	2023-06-24 09:34:00	2023-06-24 09:34:00
6	MNU_001_post	web	2023-06-24 09:34:00	2023-06-24 09:34:00
7	MNU_002_view	web	2023-06-24 09:50:15	2023-06-24 09:50:15
8	MNU_002_create	web	2023-06-24 09:50:15	2023-06-24 09:50:15
9	MNU_002_update	web	2023-06-24 09:50:15	2023-06-24 09:50:15
10	MNU_002_print	web	2023-06-24 09:50:15	2023-06-24 09:50:15
11	MNU_002_post	web	2023-06-24 09:50:15	2023-06-24 09:50:15
17	HDR_001_view	web	2023-07-01 12:03:50	2023-07-01 12:03:50
18	HDR_001_create	web	2023-07-01 12:03:50	2023-07-01 12:03:50
19	HDR_001_update	web	2023-07-01 12:03:50	2023-07-01 12:03:50
20	HDR_001_print	web	2023-07-01 12:03:50	2023-07-01 12:03:50
21	HDR_001_post	web	2023-07-01 12:03:50	2023-07-01 12:03:50
22	MNU_003_view	web	2023-07-05 11:40:07	2023-07-05 11:40:07
23	MNU_003_create	web	2023-07-05 11:40:07	2023-07-05 11:40:07
24	MNU_003_update	web	2023-07-05 11:40:07	2023-07-05 11:40:07
25	MNU_003_print	web	2023-07-05 11:40:07	2023-07-05 11:40:07
26	MNU_003_post	web	2023-07-05 11:40:07	2023-07-05 11:40:07
27	MNU_004_view	web	2023-07-07 15:12:33	2023-07-07 15:12:33
28	MNU_004_create	web	2023-07-07 15:12:33	2023-07-07 15:12:33
29	MNU_004_update	web	2023-07-07 15:12:33	2023-07-07 15:12:33
30	MNU_004_print	web	2023-07-07 15:12:33	2023-07-07 15:12:33
31	MNU_004_post	web	2023-07-07 15:12:33	2023-07-07 15:12:33
32	HDR_002_view	web	2023-07-07 15:59:09	2023-07-07 15:59:09
33	HDR_002_create	web	2023-07-07 15:59:09	2023-07-07 15:59:09
34	HDR_002_update	web	2023-07-07 15:59:09	2023-07-07 15:59:09
35	HDR_002_print	web	2023-07-07 15:59:09	2023-07-07 15:59:09
36	HDR_002_post	web	2023-07-07 15:59:09	2023-07-07 15:59:09
37	HDR_003_view	web	2023-07-07 16:32:04	2023-07-07 16:32:04
38	HDR_003_create	web	2023-07-07 16:32:04	2023-07-07 16:32:04
39	HDR_003_update	web	2023-07-07 16:32:04	2023-07-07 16:32:04
40	HDR_003_print	web	2023-07-07 16:32:04	2023-07-07 16:32:04
41	HDR_003_post	web	2023-07-07 16:32:04	2023-07-07 16:32:04
42	MNU_005_view	web	2023-07-07 16:33:24	2023-07-07 16:33:24
43	MNU_005_create	web	2023-07-07 16:33:24	2023-07-07 16:33:24
44	MNU_005_update	web	2023-07-07 16:33:24	2023-07-07 16:33:24
45	MNU_005_print	web	2023-07-07 16:33:24	2023-07-07 16:33:24
46	MNU_005_post	web	2023-07-07 16:33:24	2023-07-07 16:33:24
47	MNU_006_view	web	2023-07-07 16:34:12	2023-07-07 16:34:12
48	MNU_006_create	web	2023-07-07 16:34:12	2023-07-07 16:34:12
49	MNU_006_update	web	2023-07-07 16:34:12	2023-07-07 16:34:12
50	MNU_006_print	web	2023-07-07 16:34:12	2023-07-07 16:34:12
51	MNU_006_post	web	2023-07-07 16:34:12	2023-07-07 16:34:12
52	MNU_007_view	web	2023-07-11 10:59:07	2023-07-11 10:59:07
53	MNU_007_create	web	2023-07-11 10:59:07	2023-07-11 10:59:07
54	MNU_007_update	web	2023-07-11 10:59:07	2023-07-11 10:59:07
55	MNU_007_print	web	2023-07-11 10:59:07	2023-07-11 10:59:07
56	MNU_007_post	web	2023-07-11 10:59:07	2023-07-11 10:59:07
12	MNU_008_view	web	2023-06-30 13:16:01	2023-06-30 13:16:01
13	MNU_008_create	web	2023-06-30 13:16:01	2023-06-30 13:16:01
14	MNU_008_update	web	2023-06-30 13:16:01	2023-06-30 13:16:01
15	MNU_008_print	web	2023-06-30 13:16:01	2023-06-30 13:16:01
16	MNU_008_post	web	2023-06-30 13:16:01	2023-06-30 13:16:01
57	HDR_PR_view	web	2023-07-11 12:13:14	2023-07-11 12:13:14
58	HDR_PR_create	web	2023-07-11 12:13:14	2023-07-11 12:13:14
59	HDR_PR_update	web	2023-07-11 12:13:14	2023-07-11 12:13:14
60	HDR_PR_print	web	2023-07-11 12:13:14	2023-07-11 12:13:14
61	HDR_PR_post	web	2023-07-11 12:13:14	2023-07-11 12:13:14
62	HDR_SITE_view	web	2023-07-17 10:12:36	2023-07-17 10:12:36
63	HDR_SITE_create	web	2023-07-17 10:12:36	2023-07-17 10:12:36
64	HDR_SITE_update	web	2023-07-17 10:12:36	2023-07-17 10:12:36
65	HDR_SITE_print	web	2023-07-17 10:12:36	2023-07-17 10:12:36
66	HDR_SITE_post	web	2023-07-17 10:12:36	2023-07-17 10:12:36
67	MNU_COM_view	web	2023-07-17 10:14:07	2023-07-17 10:14:07
68	MNU_COM_create	web	2023-07-17 10:14:07	2023-07-17 10:14:07
69	MNU_COM_update	web	2023-07-17 10:14:07	2023-07-17 10:14:07
70	MNU_COM_print	web	2023-07-17 10:14:07	2023-07-17 10:14:07
71	MNU_COM_post	web	2023-07-17 10:14:07	2023-07-17 10:14:07
72	HDR_IA_view	web	2023-07-26 09:49:34	2023-07-26 09:49:34
73	HDR_IA_create	web	2023-07-26 09:49:34	2023-07-26 09:49:34
74	HDR_IA_update	web	2023-07-26 09:49:34	2023-07-26 09:49:34
75	HDR_IA_print	web	2023-07-26 09:49:34	2023-07-26 09:49:34
76	HDR_IA_post	web	2023-07-26 09:49:34	2023-07-26 09:49:34
77	MNU_IA_IN_view	web	2023-07-26 09:50:24	2023-07-26 09:50:24
78	MNU_IA_IN_create	web	2023-07-26 09:50:24	2023-07-26 09:50:24
79	MNU_IA_IN_update	web	2023-07-26 09:50:24	2023-07-26 09:50:24
80	MNU_IA_IN_print	web	2023-07-26 09:50:24	2023-07-26 09:50:24
81	MNU_IA_IN_post	web	2023-07-26 09:50:24	2023-07-26 09:50:24
82	MNU_IA_OUT_view	web	2023-07-26 09:50:59	2023-07-26 09:50:59
83	MNU_IA_OUT_create	web	2023-07-26 09:50:59	2023-07-26 09:50:59
84	MNU_IA_OUT_update	web	2023-07-26 09:50:59	2023-07-26 09:50:59
85	MNU_IA_OUT_print	web	2023-07-26 09:50:59	2023-07-26 09:50:59
86	MNU_IA_OUT_post	web	2023-07-26 09:50:59	2023-07-26 09:50:59
87	MNU_LOC_view	web	2023-07-28 09:28:09	2023-07-28 09:28:09
88	MNU_LOC_create	web	2023-07-28 09:28:09	2023-07-28 09:28:09
89	MNU_LOC_update	web	2023-07-28 09:28:09	2023-07-28 09:28:09
90	MNU_LOC_print	web	2023-07-28 09:28:09	2023-07-28 09:28:09
91	MNU_LOC_post	web	2023-07-28 09:28:09	2023-07-28 09:28:09
92	HDR_RPT_view	web	2023-07-29 11:48:53	2023-07-29 11:48:53
93	HDR_RPT_create	web	2023-07-29 11:48:53	2023-07-29 11:48:53
94	HDR_RPT_update	web	2023-07-29 11:48:53	2023-07-29 11:48:53
95	HDR_RPT_print	web	2023-07-29 11:48:53	2023-07-29 11:48:53
96	HDR_RPT_post	web	2023-07-29 11:48:53	2023-07-29 11:48:53
97	MNU_INV_RPT_view	web	2023-07-29 11:49:42	2023-07-29 11:49:42
98	MNU_INV_RPT_create	web	2023-07-29 11:49:42	2023-07-29 11:49:42
99	MNU_INV_RPT_update	web	2023-07-29 11:49:42	2023-07-29 11:49:42
100	MNU_INV_RPT_print	web	2023-07-29 11:49:42	2023-07-29 11:49:42
101	MNU_INV_RPT_post	web	2023-07-29 11:49:42	2023-07-29 11:49:42
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, created_at, updated_at) FROM stdin;
1	App\\Models\\User	1	api-admin	9d361743d89a8b8a8d706e2f7f2014334ce04e1a6d617b22812c18f5ec0ddac3	["*"]	2023-07-17 09:56:13	2023-07-14 11:34:31	2023-07-17 09:56:13
\.


--
-- Data for Name: po_dtl; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.po_dtl (po_id, line, item_id, qty, price_each, discount_each, price_total, remark) FROM stdin;
PO|2307H01-0002	1	2	5	250000	10000	1200000	test
PO|2307H01-0002	2	1	15	120000	0	1800000	\N
PO|2307H01-0001	1	1	5	250000	0	1250000	test
PO|2307H02-0001	1	1	7	140000	0	980000	testing
\.


--
-- Data for Name: po_mstr; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.po_mstr (po_id, sup_id, po_date, status, remark, site_created, line_count, item_count, total_price, created_by, updated_by, created_at, updated_at) FROM stdin;
PO|2307H01-0002	2	2023-07-24	CREATED	test	H01	2	20	3000000	2109049	2109049	2023-07-25 14:22:43	2023-07-26 09:45:02
PO|2307H01-0001	1	2023-07-23	CREATED	testing description update	H01	1	5	1250000	2109049	2109049	2023-07-25 14:22:05	2023-07-26 09:45:09
PO|2307H02-0001	2	2023-07-31	CREATED	Tesst	H02	1	7	980000	2109049	2109049	2023-07-31 17:16:07	2023-07-31 17:16:07
\.


--
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
17	2
32	2
7	2
22	2
23	2
37	2
27	2
28	2
17	1
2	1
3	1
4	1
32	1
7	1
12	3
8	1
9	1
22	1
23	1
24	1
37	1
52	1
53	1
54	1
47	1
48	1
49	1
42	1
43	1
44	1
62	1
67	1
27	1
87	1
88	1
89	1
57	1
12	1
13	1
14	1
72	1
77	1
78	1
79	1
82	1
83	1
84	1
92	1
97	1
100	1
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, name, guard_name, created_at, updated_at, status, role_name, created_by, updated_by) FROM stdin;
2	POS_01	web	2023-06-23 12:50:46	2023-06-23 12:50:46	t	Admin	2109049	2109049
3	POS_02	web	2023-06-30 14:37:51	2023-06-30 16:17:28	t	Inbound	2109049	2109049
1	SP_ADM	web	2023-06-23 12:50:36	2023-07-07 17:08:26	t	Super Admin	2109049	2109049
\.


--
-- Data for Name: site_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.site_user (user_id, site, "default") FROM stdin;
3	H01	f
3	H02	f
3	H03	t
2	H03	f
2	H04	t
2	H05	f
2	H06	f
1	H01	t
1	H02	f
1	H05	f
1	H06	f
\.


--
-- Data for Name: sites; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sites (site, name, company_id, active, created_by, updated_by, created_at, updated_at, site_company) FROM stdin;
151	Outlet Rengasdengklok	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
071	Outlet Cisarua	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
170	Outlet Wergu Wetan	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
058	Outlet Cimanggu	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
125	Outlet Bangetayu	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
102	Outlet Kedungwuni	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
220	Outlet Wedoro	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
104	Outlet Rancamanyar	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
126	Outlet Palebon	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
008	Outlet Ciputat	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
106	Outlet Kota Baru	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
186	Outlet Warakas	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
001	Outlet Merdeka	GJB	t	\N	\N	2023-07-13 16:58:04	2023-07-17 09:56:13	f
138	Outlet Gembong	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
054	Outlet Sayati	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
057	Outlet Keadilan	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
144	Outlet Sumber	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
005	Outlet Pamulang 1	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
018	Outlet Kotabumi	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
056	Outlet Cikampek 1	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
137	Outlet Malangnengah	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
212	Outlet Pakisaji	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
182	Outlet Kebon Pedes	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
218	Outlet Pasar Kemis	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
047	Outlet Cikarang 2	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
014	Outlet Karang Tengah	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
143	Outlet Megu Cilik	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
158	Outlet Sidoarum	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
074	Outlet Kesambi	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
062	Outlet Lopang	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
202	Outlet Rawa Kalong	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
123	Outlet Cikaret	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
216	Outlet Sedati	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
181	Outlet Cibungbulang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
K01	Kanwil Bandung	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
080	Outlet Banjaran	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
039	Outlet Buah Batu	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
045	Outlet Cimahi 2	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
094	Outlet Adiwerna	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
095	Outlet Kejambon	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
076	Outlet Dakota	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
203	Outlet Keradenan	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
043	Outlet Sepatan	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
K02	Kanwil Semarang	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
020	Outlet Cikarang 1	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
050	Outlet Tasikmalaya	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
088	Outlet Rajeg	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
105	Outlet Soreang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
033	Outlet Rawa Lumbu	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
P01	Penjualan HO	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
P02	Penjualan Bandung	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
085	Outlet Batu Jajar	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
036	Outlet Karawang 1	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
087	Outlet Ciapus	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
171	Outlet Prambatan	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
196	Outlet Sukatani	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
042	Outlet Ujung Berung 1	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
169	Outlet Brebes	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
119	Outlet Pemalang	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
115	Outlet Purbalingga	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
059	Outlet Karawang 4	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
213	Outlet Gedangan	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
081	Outlet Sukaregang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
096	Outlet Debong Kidul	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
134	Outlet Pandeglang	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
030	Outlet Plumpang	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
H07	Kusuma Dwipa Gadai	KDG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
110	Outlet Cihampelas	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
052	Outlet Bojong Gede	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
136	Outlet Cisoka	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
031	Outlet Surya Kencana	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
101	Outlet Banyurip	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
147	Outlet Cilembang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
009	Outlet Ciledug	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
112	Outlet Bambu Larangan	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
078	Outlet Cipedes	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
226	Outlet Cinere	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
066	Outlet Ciomas	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
032	Outlet Jatimulya	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
164	Outlet Sumedang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
234	Outlet Cengkong	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
222	Outlet Rungkut	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
227	Outlet Panjer	KDG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
232	Outlet Bintara	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
027	Outlet Cimahi 1	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
168	Outlet Pamoyanan	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
019	Outlet Tambun	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
165	Outlet Tarogong	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
188	Outlet Sumber Sari	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
233	Outlet Duren Jaya	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
236	Outlet Leuwiliang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
173	Outlet Ungaran	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
022	Outlet Juanda	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
239	Outlet Pabuaran	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
075	Outlet Cigadung	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
176	Outlet Kadipiro	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
021	Outlet Kedung Halang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
228	Outlet Sesetan	KDG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
157	Outlet Gejayan	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
086	Outlet Ciampea	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
229	Outlet Antasura	KDG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
240	Outlet Cibabat	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
235	Outlet Cilimus	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
089	Outlet Melong	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
237	Outlet Kencana Bogor	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
044	Outlet Pakojan	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
219	Outlet Galunggung	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
238	Outlet Majalengka	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
208	Outlet Rancaekek	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
041	Outlet Cibiru	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
146	Outlet Juminahan	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
198	Outlet Cilegon 2	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
184	Outlet Cidahu	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
046	Outlet Cikupa 2	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
183	Outlet Cibadak	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
035	Outlet Pamulang 2	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
230	Outlet Sumerta	KDG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
P04	Penjualan Alsut	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
130	Outlet Serengan	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
204	Outlet Pancoran Mas	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
024	Outlet Serpong 2	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
185	Outlet Mangunjaya	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
166	Outlet Manonjaya	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
P03	Penjualan Jateng	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
209	Outlet Beringin	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
083	Outlet Telagasari	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
224	Outlet Bugis	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
132	Outlet Warunggunung	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
159	Outlet Gading	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
156	Outlet Kota Gede	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
002	Outlet Serpong 1	GJB	t	\N	\N	2023-07-13 16:58:04	2023-07-17 09:56:13	f
139	Outlet Cipacung	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
063	Outlet Wanaherang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
011	Outlet Jatake	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
079	Outlet Cianjur	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
128	Outlet Laweyan	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
133	Outlet Korelet	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
192	Outlet Gadang	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
111	Outlet Padalarang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
069	Outlet Ujung Berung 2	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
114	Outlet Cikutra	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
029	Outlet Sumur Batu	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
223	Outlet Sulfat	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
201	Outlet Sirnagalih	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
225	Outlet Pasar Serang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
040	Outlet Karawang 2	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
073	Outlet Ciawi	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
210	Outlet Ciseeng	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
090	Outlet Cicurug	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
215	Outlet Wage Bangah	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
048	Outlet Karawang 3	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
124	Outlet Kaliwungu	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
016	Outlet Perum	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
092	Outlet Pedongkelan	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
084	Outlet Munjul Jaya	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
135	Outlet Jambe	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
053	Outlet Beji	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
117	Outlet Sokaraja	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
108	Outlet Fajar Baru	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
072	Outlet Kedawung	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
214	Outlet Sumokali	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
109	Outlet Kamasan	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
060	Outlet Cikampek 2	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
153	Outlet Majalaya	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
211	Outlet Tegal Harum	KDG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
142	Outlet Pangeran Drajat	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
231	Outlet A.Yani Utara	KDG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
004	Outlet Cipondoh	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
180	Outlet Citeureup	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
194	Outlet Tugu Asem	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
121	Outlet Bayongbong	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
221	Outlet Bandulan	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
191	Outlet Dinoyo	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
178	Outlet Bantul	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
118	Outlet Cilacap	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
113	Outlet Kampung Melayu	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
015	Outlet Raden Fatah	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
205	Outlet Tapos	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
161	Outlet Cibarusah	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
217	Outlet Taktakan	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
023	Outlet Serang	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
038	Outlet Curug	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
141	Outlet Rangkas	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
190	Outlet Gajah Raya	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
097	Outlet Katapang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
098	Outlet Cicalengka	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
207	Outlet Solokan	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
091	Outlet Sukaseuri	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
174	Outlet Boyolali	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
017	Outlet Cikupa 1	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
195	Outlet Bosih	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
167	Outlet Batu Ampar	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
000	Head Office	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
175	Outlet Cincin	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
206	Outlet Ujung Harapan	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
026	Outlet Kopo	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
012	Outlet Kebon Besar	GJB	t	\N	\N	2023-07-13 17:02:02	2023-07-17 09:56:13	f
051	Outlet Cibaduyut	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
155	Outlet Tanah Merah	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
049	Outlet Ciruas	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
149	Outlet Lembur Situ	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
200	Outlet Rempoa	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
131	Outlet Palur	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
150	Outlet Sukaraja	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
189	Outlet Meruyung	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
H02	Amanah Terima Gadai	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	t
003	Outlet Pondok Arum	GJB	t	\N	\N	2023-07-13 16:58:04	2023-07-17 09:56:13	f
197	Outlet Gabus	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
199	Outlet Ciputat 2	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
129	Outlet Nusukan	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
077	Outlet Anyer	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
148	Outlet Lengkong Sari	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
160	Outlet Monjali	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
140	Outlet Binong	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
065	Outlet Marga Cinta	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
107	Outlet Kondang Jaya	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
120	Outlet Singaparna	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
122	Outlet Pademangan	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
154	Outlet Cileunyi	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
116	Outlet Purwokerto	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
145	Outlet Jembatan Besi	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
100	Outlet Wiradesa	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
172	Outlet Mranggen	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
082	Outlet Jatiwangi	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
162	Outlet Setu	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
179	Outlet Buaran	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
H06	Semeru Agung Gadai	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	t
H01	Gadai Jadi Berkah	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	t
H05	Setia Indah Gadai	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	t
H04	Indah Jaya Gadai	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	t
H03	Gadai Elektronik Jakarta	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	t
067	Outlet Cipare	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
006	Outlet Cimone	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
061	Outlet Purwakarta	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
193	Outlet Sukun	SAG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
103	Outlet Kedungmundu	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
055	Outlet Sentosa	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
037	Outlet Cilegon	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
099	Outlet Kalisari	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
070	Outlet Cisaat	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
068	Outlet Lio Baru	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
093	Outlet Cidodol	GEJ	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
064	Outlet Jayaraga	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
025	Outlet Kaliabang	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
152	Outlet Ciparay	AG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
177	Outlet Banguntapan	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
187	Outlet Cijengir	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
163	Outlet Jakal	SIG	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
010	Outlet Cikokol	GJB	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
127	Outlet Kartosuro	IJG1	t	\N	\N	2023-07-17 09:54:39	2023-07-17 09:56:13	f
\.


--
-- Data for Name: suppliers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.suppliers (id, code, name, phone1, phone2, address, "desc", active, created_by, updated_by, created_at, updated_at) FROM stdin;
1	SP-001	Testing Supplier	1231241244	\N	Test address	\N	t	2109049	2109049	2023-07-11 11:56:17	2023-07-11 12:04:21
2	SP-002	PT. ABCD	1234124	\N	Test alamat	\N	t	2109049	2109049	2023-07-25 13:56:47	2023-07-25 13:56:47
\.


--
-- Data for Name: uom; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.uom (id, code, name, short_name, active, created_by, updated_by, created_at, updated_at) FROM stdin;
2	UOM_002	Kilogram	Kg	t	2109049	2109049	2023-07-11 09:30:29	2023-07-11 09:30:29
1	UOM_001	Pieces	pcs	t	2109049	2109049	2023-07-08 10:26:57	2023-07-11 09:30:48
3	UOM_003	Rim	rim	t	2109049	2109049	2023-07-11 10:18:06	2023-07-11 10:18:06
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (user_id, username, name, password, role_id, remember_token, created_by, updated_by, created_at, updated_at, status) FROM stdin;
2	111111	Test User	$2y$10$VWaFhY8748SXjIU9CCNq1uhZQQfnME2OrIhudNJINmcpDmV56HZb2	2	\N	2109049	2109049	2023-06-23 12:51:35	2023-06-23 13:17:09	t
1	2109049	Ario Nugroho	$2y$10$tw8q1dSlj1U9bqDBUTOOn.vS0GAwVgaZt5QRV/bF28hoTUCBshMrW	1	\N	TESTING	2109049	2023-06-23 12:49:27	2023-06-30 15:50:11	t
3	222222	Test User 2	$2y$10$QLoy4ANni1i4.J5G7itfIuFu/7PnphVm.i4RBnZ8hslxkiMvFkkgm	3	\N	2109049	2109049	2023-07-07 15:25:57	2023-07-07 15:25:57	t
\.


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: inv_hist_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_hist_id_seq', 8, true);


--
-- Name: items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.items_id_seq', 2, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 64, true);


--
-- Name: modules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.modules_id_seq', 22, true);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.permissions_id_seq', 101, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, true);


--
-- Name: role_mstr_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.role_mstr_role_id_seq', 7, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 3, true);


--
-- Name: suppliers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.suppliers_id_seq', 2, true);


--
-- Name: uom_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.uom_id_seq', 3, true);


--
-- Name: users_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_user_id_seq', 3, true);


--
-- Name: companies companies_company_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.companies
    ADD CONSTRAINT companies_company_unique UNIQUE (company);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: inv_hist inv_hist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_hist
    ADD CONSTRAINT inv_hist_pkey PRIMARY KEY (id);


--
-- Name: items items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.items
    ADD CONSTRAINT items_pkey PRIMARY KEY (id);


--
-- Name: locations locations_loc_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_loc_id_unique UNIQUE (loc_id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: modules modules_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.modules
    ADD CONSTRAINT modules_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: po_mstr po_mstr_po_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.po_mstr
    ADD CONSTRAINT po_mstr_po_id_unique UNIQUE (po_id);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: roles roles_role_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_role_name_unique UNIQUE (role_name);


--
-- Name: sites sites_site_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sites
    ADD CONSTRAINT sites_site_unique UNIQUE (site);


--
-- Name: suppliers suppliers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id);


--
-- Name: uom uom_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.uom
    ADD CONSTRAINT uom_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- Name: users users_username_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_unique UNIQUE (username);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: password_resets_email_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX password_resets_email_index ON public.password_resets USING btree (email);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: inv_hist inv_hist_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_hist
    ADD CONSTRAINT inv_hist_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.items(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: inv_hist inv_hist_loc_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_hist
    ADD CONSTRAINT inv_hist_loc_id_foreign FOREIGN KEY (loc_id) REFERENCES public.locations(loc_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: inv_hist inv_hist_site_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_hist
    ADD CONSTRAINT inv_hist_site_id_foreign FOREIGN KEY (site_id) REFERENCES public.sites(site) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: inventory inventory_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventory
    ADD CONSTRAINT inventory_item_id_foreign FOREIGN KEY (item_id) REFERENCES public.items(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: inventory inventory_loc_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventory
    ADD CONSTRAINT inventory_loc_id_foreign FOREIGN KEY (loc_id) REFERENCES public.locations(loc_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: inventory inventory_site_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventory
    ADD CONSTRAINT inventory_site_id_foreign FOREIGN KEY (site_id) REFERENCES public.sites(site) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: locations locations_site_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_site_id_foreign FOREIGN KEY (site_id) REFERENCES public.sites(site);


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: po_mstr po_mstr_sup_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.po_mstr
    ADD CONSTRAINT po_mstr_sup_id_foreign FOREIGN KEY (sup_id) REFERENCES public.suppliers(id);


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: site_user site_user_site_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site_user
    ADD CONSTRAINT site_user_site_foreign FOREIGN KEY (site) REFERENCES public.sites(site);


--
-- Name: site_user site_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site_user
    ADD CONSTRAINT site_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(user_id);


--
-- PostgreSQL database dump complete
--

