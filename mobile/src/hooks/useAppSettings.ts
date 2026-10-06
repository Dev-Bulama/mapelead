import { useQuery } from '@tanstack/react-query';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';

interface AppSettings {
  logo_url: string | null;
  app_name: string;
  tagline:  string;
}

export function useAppSettings() {
  return useQuery<AppSettings>({
    queryKey: ['app-settings'],
    queryFn:  async () => {
      const res = await apiClient.get<{ data: AppSettings }>(API.APP_SETTINGS);
      return res.data.data;
    },
    staleTime: 1000 * 60 * 60, // 1 hour
    retry: false,
  });
}
