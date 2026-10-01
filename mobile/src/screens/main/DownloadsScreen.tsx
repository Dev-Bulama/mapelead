import React, { useState, useCallback } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, Alert, Linking,
} from 'react-native';
import * as FileSystem from 'expo-file-system';
import { useFocusEffect, useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

export const DOWNLOADS_DIR = (FileSystem.documentDirectory ?? '') + 'mapelead_downloads/';

interface DownloadedFile {
  name: string;
  uri:  string;
  size: number;
  modifiedAt?: number;
}

async function ensureDir() {
  await FileSystem.makeDirectoryAsync(DOWNLOADS_DIR, { intermediates: true }).catch(() => {});
}

export async function downloadAttachment(url: string, filename: string): Promise<string> {
  await ensureDir();
  // Sanitize filename
  const safe = filename.replace(/[^a-zA-Z0-9._-]/g, '_');
  const dest = DOWNLOADS_DIR + safe;
  const result = await FileSystem.downloadAsync(url, dest);
  return result.uri;
}

async function listDownloads(): Promise<DownloadedFile[]> {
  await ensureDir();
  try {
    const names = await FileSystem.readDirectoryAsync(DOWNLOADS_DIR);
    const infos = await Promise.all(
      names.map(async (name) => {
        const uri  = DOWNLOADS_DIR + name;
        const info = await FileSystem.getInfoAsync(uri, { size: true });
        if (!info.exists) return null;
        return {
          name,
          uri,
          size:       (info as any).size ?? 0,
          modifiedAt: (info as any).modificationTime,
        } as DownloadedFile;
      })
    );
    return infos
      .filter((f): f is DownloadedFile => f !== null)
      .sort((a, b) => (b.modifiedAt ?? 0) - (a.modifiedAt ?? 0));
  } catch {
    return [];
  }
}

function formatSize(bytes: number): string {
  if (bytes < 1024)       return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];
function fileIcon(name: string): IoniconsName {
  const ext = name.split('.').pop()?.toLowerCase() ?? '';
  if (ext === 'pdf')                           return 'document-text-outline';
  if (['mp4','mov','avi'].includes(ext))       return 'videocam-outline';
  if (['mp3','wav','aac','m4a'].includes(ext)) return 'musical-notes-outline';
  if (['jpg','jpeg','png','gif','webp'].includes(ext)) return 'image-outline';
  if (['zip','rar','7z','tar','gz'].includes(ext))     return 'archive-outline';
  if (['ppt','pptx'].includes(ext))            return 'easel-outline';
  if (['doc','docx'].includes(ext))            return 'document-outline';
  if (['xls','xlsx','csv'].includes(ext))      return 'grid-outline';
  return 'attach-outline';
}

export default function DownloadsScreen() {
  const navigation = useNavigation<Nav>();
  const [files,   setFiles]   = useState<DownloadedFile[]>([]);
  const [loading, setLoading] = useState(true);

  const load = useCallback(async () => {
    setLoading(true);
    const result = await listDownloads();
    setFiles(result);
    setLoading(false);
  }, []);

  useFocusEffect(useCallback(() => { load(); }, [load]));

  const handleOpen = async (file: DownloadedFile) => {
    try {
      const supported = await Linking.canOpenURL(file.uri);
      if (supported) {
        await Linking.openURL(file.uri);
      } else {
        Alert.alert('Cannot open', 'No app available to open this file type.');
      }
    } catch {
      Alert.alert('Error', 'Could not open the file.');
    }
  };

  const handleDelete = (file: DownloadedFile) => {
    Alert.alert('Delete file', `Remove "${file.name}" from downloads?`, [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Delete', style: 'destructive',
        onPress: async () => {
          await FileSystem.deleteAsync(file.uri, { idempotent: true });
          setFiles(prev => prev.filter(f => f.uri !== file.uri));
        },
      },
    ]);
  };

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <Text style={S.title}>Downloads</Text>
        <Text style={S.subtitle}>{files.length} file{files.length !== 1 ? 's' : ''} saved</Text>
      </View>

      {loading ? (
        <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>
      ) : !files.length ? (
        <View style={S.empty}>
          <Ionicons name="cloud-download-outline" size={56} color={Colors.gray300} />
          <Text style={S.emptyTitle}>No downloads yet</Text>
          <Text style={S.emptyText}>
            Tap "Download Attachment" on any lesson to save it here for offline viewing.
          </Text>
          <TouchableOpacity
            style={S.browseBtn}
            onPress={() => navigation.navigate('Main', { screen: 'MyLearning' } as any)}
          >
            <Text style={S.browseBtnText}>Go to My Courses</Text>
          </TouchableOpacity>
        </View>
      ) : (
        <FlatList
          data={files}
          keyExtractor={(item) => item.uri}
          contentContainerStyle={S.list}
          renderItem={({ item }) => (
            <TouchableOpacity style={S.card} onPress={() => handleOpen(item)} activeOpacity={0.75}>
              <View style={S.iconWrap}>
                <Ionicons name={fileIcon(item.name)} size={26} color={Colors.primary} />
              </View>
              <View style={S.info}>
                <Text style={S.fileName} numberOfLines={2}>{item.name}</Text>
                <Text style={S.meta}>{formatSize(item.size)}</Text>
              </View>
              <TouchableOpacity
                style={S.deleteBtn}
                onPress={() => handleDelete(item)}
                hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
              >
                <Ionicons name="trash-outline" size={18} color={Colors.error} />
              </TouchableOpacity>
            </TouchableOpacity>
          )}
        />
      )}
    </View>
  );
}

const S = StyleSheet.create({
  screen:     { flex: 1, backgroundColor: Colors.surface },
  header:     { backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[4], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  title:      { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  subtitle:   { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginTop: 2 },
  center:     { flex: 1, justifyContent: 'center', alignItems: 'center' },
  empty:      { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: Spacing[8], gap: Spacing[4] },
  emptyTitle: { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptyText:  { fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center', marginTop: Spacing[2], lineHeight: 22 },
  browseBtn:  { marginTop: Spacing[4], backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingHorizontal: Spacing[6], paddingVertical: Spacing[3] },
  browseBtnText: { color: Colors.white, fontWeight: Typography.weights.semibold, fontSize: Typography.sizes.base },
  list:       { padding: Spacing[4], gap: Spacing[3] },
  card:       { flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], gap: Spacing[3], ...Shadows.sm },
  iconWrap:   { width: 50, height: 50, borderRadius: 12, backgroundColor: Colors.gray100, alignItems: 'center', justifyContent: 'center' },
  info:       { flex: 1 },
  fileName:   { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, lineHeight: 18, marginBottom: 4 },
  meta:       { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  deleteBtn:  { padding: 4 },
});
