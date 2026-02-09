export interface NewsSource {
  id: number;
  name: string;
  slug: string;
  url: string;
}

export interface MiningJurisdiction {
  id: number;
  name: string;
  slug: string;
  country_code?: string;
}

export interface Commodity {
  id: number;
  name: string;
  slug: string;
  symbol?: string;
}

export interface Company {
  id: number;
  name: string;
  slug: string;
  symbol?: string;
}

export interface MiningCategory {
  id: number;
  name: string;
  slug: string;
  type?: string;
}

export interface DrillResult {
  id: number;
  hole_id?: string;
  intercept_m?: number;
  grade?: number;
  unit?: string;
  commodity?: Commodity;
  mining_article?: { id: number; slug: string; title: string };
}

export interface MiningArticle {
  id: number;
  slug: string;
  title: string;
  excerpt?: string;
  content?: string;
  url: string;
  image_url?: string;
  author?: string;
  published_at?: string;
  view_count: number;
  news_source?: NewsSource;
  mining_jurisdiction?: MiningJurisdiction | null;
  commodities?: Commodity[];
  companies?: Company[];
  mining_categories?: MiningCategory[];
  drill_results?: DrillResult[];
  metadata?: Record<string, unknown>;
}

export interface PaginatedMiningArticles {
  data: MiningArticle[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  links: { url: string | null; label: string; active: boolean }[];
}

export interface CommodityPrice {
  id: number;
  symbol: string;
  name: string;
  type: string;
  price_usd: string;
  previous_price_usd: string | null;
  change_24h_percent: string | null;
  fetched_at: string | null;
}

export interface PaginatedDrillResults {
  data: DrillResult[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  links: { url: string | null; label: string; active: boolean }[];
}
