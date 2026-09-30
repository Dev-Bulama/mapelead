import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  ScrollView, Image, Alert, ActivityIndicator,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import * as ImagePicker from 'expo-image-picker';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { extractApiError } from '@/api/client';
import { useAuthStore, selectUser } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, User, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'ProfileEdit'>;

export default function ProfileEditScreen({ navigation }: Props) {
  const user          = useAuthStore(selectUser);
  const refreshUser   = useAuthStore((s) => s.refreshUser);
  const qc            = useQueryClient();

  const [form, setForm] = useState({
    first_name:    user?.first_name ?? '',
    last_name:     user?.last_name  ?? '',
    phone:         user?.phone      ?? '',
    bio:           (user as any)?.bio ?? '',
  });
  const [avatarUri, setAvatarUri] = useState<string | null>(null);

  const set = (key: keyof typeof form) => (val: string) =>
    setForm((p) => ({ ...p, [key]: val }));

  const { mutate: saveProfile, isPending: saving } = useMutation({
    mutationFn: () =>
      apiClient.put<ApiResponse<Partial<User>>>(API.PROFILE, form),
    onSuccess: async () => {
      await refreshUser();
      Alert.alert('Saved', 'Profile updated successfully.');
      navigation.goBack();
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const { mutate: uploadAvatar, isPending: uploading } = useMutation({
    mutationFn: async (uri: string) => {
      const form = new FormData();
      form.append('avatar', { uri, name: 'avatar.jpg', type: 'image/jpeg' } as any);
      return apiClient.post<ApiResponse<{ avatar_url: string }>>(API.PROFILE_AVATAR, form, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
    },
    onSuccess: async () => {
      await refreshUser();
      Alert.alert('Done', 'Avatar updated.');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const pickAvatar = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permission required', 'Allow photo library access to change your avatar.');
      return;
    }
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [1, 1],
      quality: 0.8,
    });
    if (!result.canceled && result.assets?.[0]) {
      const uri = result.assets[0].uri;
      setAvatarUri(uri);
      uploadAvatar(uri);
    }
  };

  const displayAvatar = avatarUri ?? user?.avatar_url;

  return (
    <View style={s.flex}>
      <View style={s.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={s.cancelBtn}>Cancel</Text>
        </TouchableOpacity>
        <Text style={s.headerTitle}>Edit Profile</Text>
        <TouchableOpacity
          onPress={() => saveProfile()}
          disabled={saving}
        >
          <Text style={[s.saveBtn, saving && s.saveBtnDisabled]}>
            {saving ? 'Saving…' : 'Save'}
          </Text>
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={s.scroll}>
        {/* Avatar */}
        <View style={s.avatarSection}>
          <TouchableOpacity onPress={pickAvatar} disabled={uploading}>
            {displayAvatar ? (
              <Image source={{ uri: displayAvatar }} style={s.avatar} />
            ) : (
              <View style={s.avatarFallback}>
                <Text style={s.avatarInitials}>
                  {(user?.first_name?.[0] ?? '') + (user?.last_name?.[0] ?? '')}
                </Text>
              </View>
            )}
            <View style={s.avatarEditBadge}>
              {uploading ? <ActivityIndicator size="small" color={Colors.white} /> : <Text>📷</Text>}
            </View>
          </TouchableOpacity>
          <Text style={s.avatarHint}>Tap to change photo</Text>
        </View>

        {/* Form */}
        <View style={s.card}>
          <Field label="First Name" value={form.first_name} onChangeText={set('first_name')} placeholder="Ada" />
          <Field label="Last Name"  value={form.last_name}  onChangeText={set('last_name')}  placeholder="Lovelace" />
          <Field label="Phone"      value={form.phone}      onChangeText={set('phone')}       placeholder="+234 000 000 0000" keyboardType="phone-pad" />
          <Field label="Bio"        value={form.bio}        onChangeText={set('bio')}         placeholder="Tell us about yourself…" multiline />
        </View>

        {/* Change password link */}
        <TouchableOpacity style={s.changePasswordBtn}>
          <Text style={s.changePasswordText}>🔒 Change Password</Text>
        </TouchableOpacity>
      </ScrollView>
    </View>
  );
}

function Field({
  label, value, onChangeText, placeholder, keyboardType, multiline,
}: {
  label: string; value: string; onChangeText: (v: string) => void;
  placeholder?: string; keyboardType?: any; multiline?: boolean;
}) {
  return (
    <View style={s.field}>
      <Text style={s.fieldLabel}>{label}</Text>
      <TextInput
        style={[s.input, multiline && s.inputMultiline]}
        value={value}
        onChangeText={onChangeText}
        placeholder={placeholder}
        placeholderTextColor={Colors.gray400}
        keyboardType={keyboardType ?? 'default'}
        multiline={multiline}
        numberOfLines={multiline ? 3 : 1}
        textAlignVertical={multiline ? 'top' : 'center'}
      />
    </View>
  );
}

const s = StyleSheet.create({
  flex:             { flex: 1, backgroundColor: Colors.surface },
  header:           { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingTop: Spacing[12], paddingHorizontal: Spacing[4], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.border },
  cancelBtn:        { fontSize: Typography.sizes.base, color: Colors.gray600, fontWeight: Typography.weights.medium },
  headerTitle:      { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  saveBtn:          { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.bold },
  saveBtnDisabled:  { opacity: 0.5 },
  scroll:           { padding: Spacing[4] },
  avatarSection:    { alignItems: 'center', marginBottom: Spacing[6] },
  avatar:           { width: 96, height: 96, borderRadius: Radii.full, borderWidth: 3, borderColor: Colors.primary },
  avatarFallback:   { width: 96, height: 96, borderRadius: Radii.full, backgroundColor: Colors.primary, justifyContent: 'center', alignItems: 'center', borderWidth: 3, borderColor: Colors.primaryDark },
  avatarInitials:   { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.white },
  avatarEditBadge:  { position: 'absolute', bottom: 0, right: 0, backgroundColor: Colors.primaryDark, borderRadius: Radii.full, width: 28, height: 28, justifyContent: 'center', alignItems: 'center', borderWidth: 2, borderColor: Colors.white },
  avatarHint:       { marginTop: Spacing[2], fontSize: Typography.sizes.sm, color: Colors.textMuted },
  card:             { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], ...Shadows.sm, marginBottom: Spacing[4] },
  field:            { marginBottom: Spacing[4] },
  fieldLabel:       { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.gray700, marginBottom: Spacing[1] },
  input:            { borderWidth: 1, borderColor: Colors.gray300, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary },
  inputMultiline:   { minHeight: 80, paddingTop: Spacing[3] },
  changePasswordBtn:{ backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], alignItems: 'center', ...Shadows.sm },
  changePasswordText:{ fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium },
});
